<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class SaleUpdateQuantityTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_update_sale_quantity_adjusts_stock(): void
    {
        $tenant = $this->readyTenant();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'stock_quantity' => 10,
        ]);
        $sale = Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 10,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);

        $this->actingAs($tenant, 'tenant')
            ->put(route('tenant.sales.update', $sale), [
                'quantity' => 4,
                'unit_price' => 10,
                'payment_method' => 'pix',
                'sold_at' => now()->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('tenant.sales.index'));

        $this->assertSame(8, $product->fresh()->stock_quantity);
        $this->assertSame(4, $sale->fresh()->quantity);
    }
}
