<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\Billing\StripeBillingService;
use App\Services\Billing\SubscriptionLifecycleService;
use App\Services\PaymentService;
use Database\Seeders\PlanSeeder;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_activate_premium_upgrades_tenant_and_creates_subscription(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create(['plan' => 'basic']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        $service = app(SubscriptionLifecycleService::class);
        $result = $service->activatePremium($tenant->id, $plan->id, 'sub_test_123', null, null);

        $this->assertTrue($result);
        $this->assertTrue($tenant->fresh()->isPremium());
        $this->assertSame('sub_test_123', $tenant->fresh()->subscription?->stripe_subscription_id);
    }

    public function test_pix_checkout_is_cached_and_not_regenerated(): void
    {
        $this->seed(PlanSeeder::class);
        config(['precifique.pix.pending_ttl_minutes' => 30]);

        Http::fake([
            'api.mercadopago.com/v1/payments' => Http::response([
                'id' => 'pay_123',
                'point_of_interaction' => [
                    'transaction_data' => [
                        'qr_code' => 'pix-copy-code',
                        'qr_code_base64' => base64_encode('qr'),
                    ],
                ],
            ], 201),
        ]);

        $tenant = Tenant::factory()->create(['plan' => 'basic']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();
        config(['services.mercadopago.access_token' => 'test-token']);
        $service = app(PaymentService::class);

        $first = $service->getOrCreateMercadoPagoPix($tenant, $plan);
        $second = $service->getOrCreateMercadoPagoPix($tenant, $plan);

        $this->assertSame('pay_123', $first['payment_id']);
        $this->assertSame($first['payment_id'], $second['payment_id']);
        Http::assertSentCount(1);
    }

    public function test_invoice_payment_failed_sets_past_due_grace_period(): void
    {
        $this->seed(PlanSeeder::class);
        config(['precifique.billing.grace_period_days' => 5]);

        $tenant = Tenant::factory()->create(['plan' => 'premium']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        $subscription = $tenant->subscription()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'stripe_subscription_id' => 'sub_fail_1',
            'starts_at' => now()->subMonth(),
            'ends_at' => null,
        ]);

        $service = app(StripeBillingService::class);
        $invoice = (object) ['subscription' => 'sub_fail_1'];
        $service->handleInvoicePaymentFailed($invoice);

        $subscription->refresh();
        $this->assertSame('past_due', $subscription->status);
        $this->assertNotNull($subscription->ends_at);
        $this->assertTrue($subscription->ends_at->isFuture());
    }

    public function test_subscription_factory_creates_active_record(): void
    {
        $subscription = Subscription::factory()->stripe('sub_factory_1')->create();

        $this->assertSame('active', $subscription->status);
        $this->assertTrue($subscription->isActive());
    }
}
