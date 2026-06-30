<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use Database\Seeders\PlanSeeder;
use Tests\Concerns\BuildsStripeWebhookSignature;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use BuildsStripeWebhookSignature;
    use RefreshDatabase;

    public function test_webhook_rejects_missing_secret(): void
    {
        config(['services.stripe.webhook_secret' => '']);

        $this->post('/webhooks/stripe', [], [
            'Stripe-Signature' => 't=1,v1=abc',
        ])->assertStatus(400);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test_secret']);

        $this->call('POST', '/webhooks/stripe', [], [], [], [
            'HTTP_Stripe-Signature' => 't=1,v1=invalid',
            'CONTENT_TYPE' => 'application/json',
        ], '{"id":"evt_1"}')
            ->assertStatus(400);
    }

    public function test_checkout_session_completed_activates_premium(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create(['plan' => 'basic']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        $payload = $this->stripeWebhookPayload([
            'id' => 'evt_checkout_completed_1',
            'data' => [
                'object' => [
                    'metadata' => [
                        'tenant_id' => (string) $tenant->id,
                        'plan_id' => (string) $plan->id,
                    ],
                    'subscription' => 'sub_checkout_123',
                ],
            ],
        ]);

        $this->postStripeWebhook($payload)->assertOk();

        $this->assertTrue($tenant->fresh()->isPremium());
        $this->assertSame('sub_checkout_123', $tenant->fresh()->subscription?->stripe_subscription_id);
    }

    public function test_checkout_webhook_is_idempotent(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create(['plan' => 'basic']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        $payload = $this->stripeWebhookPayload([
            'id' => 'evt_checkout_idempotent_1',
            'data' => [
                'object' => [
                    'metadata' => [
                        'tenant_id' => (string) $tenant->id,
                        'plan_id' => (string) $plan->id,
                    ],
                    'subscription' => 'sub_idempotent_123',
                ],
            ],
        ]);

        $this->postStripeWebhook($payload)->assertOk();
        $this->postStripeWebhook($payload)->assertOk();

        $this->assertTrue($tenant->fresh()->isPremium());
        $this->assertSame(1, $tenant->fresh()->subscription()->count());
    }

    public function test_subscription_deleted_downgrades_tenant(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create([
            'plan' => 'premium',
            'trial_ends_at' => null,
        ]);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        $tenant->subscription()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'stripe_subscription_id' => 'sub_cancel_123',
            'starts_at' => now()->subMonth(),
            'ends_at' => null,
        ]);

        $payload = $this->stripeWebhookPayload([
            'id' => 'evt_subscription_deleted_1',
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => 'sub_cancel_123',
                    'object' => 'subscription',
                    'status' => 'canceled',
                ],
            ],
        ]);

        $this->postStripeWebhook($payload)->assertOk();

        $this->assertFalse($tenant->fresh()->isPremium());
        $this->assertSame('cancelled', $tenant->fresh()->subscription?->status);
    }
}
