<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ExpireSubscriptionsJob;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\PaymentService;
use Database\Seeders\PlanSeeder;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ExpireSubscriptionsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_expires_past_due_subscriptions(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::factory()->create(['plan' => 'premium']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'mercadopago_payment_id' => 'pix_job_test',
            'starts_at' => now()->subDays(31),
            'ends_at' => now()->subDay(),
        ]);

        (new ExpireSubscriptionsJob)->handle(app(PaymentService::class));

        $this->assertSame('basic', $tenant->fresh()->plan->value);
    }
}
