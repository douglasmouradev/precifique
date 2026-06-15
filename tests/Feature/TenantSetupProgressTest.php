<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Services\TenantSetupProgressService;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class TenantSetupProgressTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_dashboard_steps_include_sale_and_goal(): void
    {
        $tenant = $this->readyTenant();
        $steps = app(TenantSetupProgressService::class)->forDashboard($tenant);
        $keys = collect($steps)->pluck('key')->all();

        $this->assertContains('sale', $keys);
        $this->assertContains('goal', $keys);
        $this->assertContains('price', $keys);
    }

    public function test_unpriced_filter_url_is_used_for_price_step(): void
    {
        $tenant = $this->readyTenant();
        Product::factory()->create([
            'tenant_id' => $tenant->id,
            'selling_price' => null,
        ]);

        $steps = app(TenantSetupProgressService::class)->forDashboard($tenant);
        $priceStep = collect($steps)->firstWhere('key', 'price');

        $this->assertFalse($priceStep['done']);
        $this->assertStringContainsString('unpriced=1', $priceStep['url']);
    }
}
