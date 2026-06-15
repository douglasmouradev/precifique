<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Services\PaymentService;
use Database\Seeders\PlanSeeder;
use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class BillingSecurityTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_stripe_success_rejects_session_for_other_tenant(): void
    {
        $this->seed(PlanSeeder::class);

        $tenantA = $this->readyTenant(['email' => 'a@test.com']);
        Tenant::factory()->create(['email' => 'b@test.com']);

        $payments = $this->mock(PaymentService::class);
        $payments->shouldReceive('isStripeSessionPaid')
            ->once()
            ->with('sess_other', $tenantA->id)
            ->andReturn(false);

        $this->actingAs($tenantA, 'tenant')
            ->get('/app/billing/success?session_id=sess_other')
            ->assertRedirect(route('tenant.billing.upgrade'))
            ->assertSessionHas('warning');
    }
}
