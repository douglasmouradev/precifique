<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\PaymentService;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pix_subscription_expires_and_downgrades_tenant(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create(['plan' => 'premium']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'mercadopago_payment_id' => 'pix_test_1',
            'starts_at' => now()->subDays(31),
            'ends_at' => now()->subDay(),
        ]);

        $count = app(PaymentService::class)->expireSubscriptions();

        $this->assertSame(1, $count);
        $this->assertSame('basic', $tenant->fresh()->plan->value);
        $this->assertSame('cancelled', $tenant->fresh()->subscription->status);
    }

    public function test_trial_tenant_keeps_premium_access_after_pix_expiry(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create([
            'plan' => 'basic',
            'trial_ends_at' => now()->addDays(7),
        ]);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now()->subDays(31),
            'ends_at' => now()->subDay(),
        ]);

        app(PaymentService::class)->expireSubscriptions();

        $this->assertSame('basic', $tenant->fresh()->plan->value);
        $this->assertTrue($tenant->fresh()->isPremium());
    }
}
