<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Sale;
use App\Services\DashboardMetricsService;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class DashboardMetricsServiceTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_dashboard_metrics_are_cached(): void
    {
        Cache::flush();
        $tenant = $this->readyTenant(['plan' => 'premium']);

        $service = app(DashboardMetricsService::class);
        $first = $service->for($tenant);
        $second = $service->for($tenant);

        $this->assertSame($first['monthRevenue'], $second['monthRevenue']);
        $this->assertTrue(Cache::has('tenant.'.$tenant->id.'.dashboard.'.now()->format('Y-m')));
    }

    public function test_forget_clears_dashboard_cache(): void
    {
        Cache::flush();
        $tenant = $this->readyTenant(['plan' => 'basic']);

        $service = app(DashboardMetricsService::class);
        $service->for($tenant);

        $product = $tenant->products()->create(['name' => 'Bolo', 'niche_type' => 'alimentos']);
        Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 50,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);

        $service->forget($tenant);
        $after = $service->for($tenant);

        $this->assertSame(50.0, (float) $after['monthRevenue']);
    }

    public function test_top_products_include_product_names(): void
    {
        Cache::flush();
        $tenant = $this->readyTenant(['plan' => 'basic']);

        $product = $tenant->products()->create([
            'name' => 'Brownie',
            'niche_type' => 'alimentos',
            'selling_price' => 12,
            'is_active' => true,
        ]);

        Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 12,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);

        $data = app(DashboardMetricsService::class)->for($tenant);

        $this->assertContains('Brownie', $data['topProductLabels']->all());
        $this->assertSame(3, (int) $data['topProductQty']->first());
    }
}
