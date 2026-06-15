<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class TenantAccessTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_can_access_main_app_pages(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);

        $routes = [
            'tenant.dashboard',
            'tenant.products.index',
            'tenant.products.create',
            'tenant.sales.index',
            'tenant.sales.create',
            'tenant.fixed-costs.index',
            'tenant.variable-costs.index',
            'tenant.stock.index',
            'tenant.goals.edit',
            'tenant.lgpd.portal',
            'tenant.billing.upgrade',
        ];

        foreach ($routes as $route) {
            $this->actingAs($tenant, 'tenant')
                ->get(route($route))
                ->assertOk("Falha ao acessar {$route}");
        }
    }

    public function test_tenant_can_logout_via_sair_route(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);

        $this->actingAs($tenant, 'tenant')
            ->post(route('tenant.logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest('tenant');
    }
}
