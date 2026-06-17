<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class MonthlyReportTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_premium_tenant_can_download_monthly_report(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);

        Product::factory()->for($tenant)->create(['name' => 'Bolo']);
        Sale::factory()->for($tenant)->create();

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.reports.index'))
            ->assertOk();

        $response = $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.reports.monthly'));

        $response->assertOk();
        $response->assertDownload();
    }

    public function test_basic_tenant_is_redirected_to_upgrade(): void
    {
        $tenant = $this->readyTenant([
            'plan' => 'basic',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.reports.monthly'))
            ->assertRedirect(route('tenant.billing.upgrade'));
    }

    public function test_premium_team_member_can_download_monthly_report(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium', 'email' => 'owner@precifique.com.br']);

        $member = \App\Models\TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Editor',
            'email' => 'editor@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'editor@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.dashboard'));

        $this->get(route('tenant.reports.monthly'))->assertOk();
    }
}
