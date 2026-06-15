<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\SaleRecorded;
use App\Models\Sale;
use App\Services\DashboardMetricsService;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class DashboardCacheEventTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_sale_recorded_event_invalidates_dashboard_cache(): void
    {
        Cache::flush();
        $tenant = $this->readyTenant(['plan' => 'premium']);
        $metrics = app(DashboardMetricsService::class);
        $metrics->for($tenant);

        $cacheKey = 'tenant.'.$tenant->id.'.dashboard.'.now()->format('Y-m');
        $this->assertTrue(Cache::has($cacheKey));

        $product = $tenant->products()->create(['name' => 'Item', 'niche_type' => 'alimentos']);
        $sale = Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);
        SaleRecorded::dispatch($tenant, $sale);

        $this->assertFalse(Cache::has($cacheKey));
    }
}
