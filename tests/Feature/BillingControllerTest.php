<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TenantMember;
use App\Services\PaymentService;
use Database\Seeders\PlanSeeder;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class BillingControllerTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_owner_can_view_upgrade_page(): void
    {
        $this->seed(PlanSeeder::class);
        $tenant = $this->readyTenant(['plan' => 'basic']);

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.billing.upgrade'))
            ->assertOk()
            ->assertViewIs('billing.upgrade');
    }

    public function test_stripe_checkout_redirects_to_payment_provider(): void
    {
        $this->seed(PlanSeeder::class);
        $tenant = $this->readyTenant(['plan' => 'basic']);

        $this->mock(PaymentService::class, function ($mock): void {
            $mock->shouldReceive('createStripeCheckout')
                ->once()
                ->andReturn('https://checkout.stripe.com/test-session');
        });

        $this->actingAs($tenant, 'tenant')
            ->post(route('tenant.billing.stripe'))
            ->assertRedirect('https://checkout.stripe.com/test-session');
    }

    public function test_success_redirects_when_tenant_already_premium(): void
    {
        $this->seed(PlanSeeder::class);
        $tenant = $this->readyTenant(['plan' => 'premium']);

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.billing.success'))
            ->assertRedirect(route('tenant.dashboard'))
            ->assertSessionHas('success');
    }

    public function test_success_redirects_when_stripe_session_is_paid(): void
    {
        $this->seed(PlanSeeder::class);
        $tenant = $this->readyTenant(['plan' => 'basic']);

        $this->mock(PaymentService::class, function ($mock) use ($tenant): void {
            $mock->shouldReceive('isStripeSessionPaid')
                ->once()
                ->with('sess_paid_1', $tenant->id)
                ->andReturn(true);
        });

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.billing.success', ['session_id' => 'sess_paid_1']))
            ->assertRedirect(route('tenant.dashboard'))
            ->assertSessionHas('success');
    }

    public function test_cancel_redirects_with_warning(): void
    {
        $tenant = $this->readyTenant(['plan' => 'basic']);

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.billing.cancel'))
            ->assertRedirect(route('tenant.billing.upgrade'))
            ->assertSessionHas('warning');
    }

    public function test_portal_redirects_when_url_available(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);

        $this->mock(PaymentService::class, function ($mock): void {
            $mock->shouldReceive('billingPortalUrl')
                ->once()
                ->andReturn('https://billing.stripe.com/portal/test');
        });

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.billing.portal'))
            ->assertRedirect('https://billing.stripe.com/portal/test');
    }

    public function test_portal_shows_warning_when_unavailable(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);

        $this->mock(PaymentService::class, function ($mock): void {
            $mock->shouldReceive('billingPortalUrl')
                ->once()
                ->andReturn(null);
        });

        $this->actingAs($tenant, 'tenant')
            ->from(route('tenant.billing.upgrade'))
            ->get(route('tenant.billing.portal'))
            ->assertRedirect(route('tenant.billing.upgrade'))
            ->assertSessionHas('warning');
    }

    public function test_pix_status_returns_premium_flag(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);

        $this->actingAs($tenant, 'tenant')
            ->getJson(route('tenant.billing.pix.status'))
            ->assertOk()
            ->assertJson(['premium' => true]);
    }

    public function test_viewer_cannot_access_billing_upgrade(): void
    {
        $this->seed(PlanSeeder::class);
        $tenant = $this->readyTenant(['email' => 'owner@precifique.com.br', 'password' => 'demo1234']);

        TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Viewer',
            'email' => 'viewer@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'viewer',
            'is_active' => true,
        ]);

        $this->post('/entrar', ['email' => 'viewer@precifique.com.br', 'password' => 'demo1234']);

        $this->get(route('tenant.billing.upgrade'))->assertForbidden();
    }
}
