<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class SaleStockTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_sale_decrements_stock_in_transaction(): void
    {
        $tenant = $this->readyTenant();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($tenant, 'tenant')
            ->post(route('tenant.sales.store'), [
                'product_id' => $product->id,
                'quantity' => 3,
                'unit_price' => 25.00,
                'payment_method' => 'pix',
            ])
            ->assertRedirect(route('tenant.sales.index'));

        $this->assertSame(7, $product->fresh()->stock_quantity);
        $this->assertSame(1, Sale::count());
    }

    public function test_sale_rejects_insufficient_stock(): void
    {
        $tenant = $this->readyTenant();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        $this->actingAs($tenant, 'tenant')
            ->from(route('tenant.sales.create'))
            ->post(route('tenant.sales.store'), [
                'product_id' => $product->id,
                'quantity' => 5,
                'unit_price' => 25.00,
                'payment_method' => 'pix',
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertSame(0, Sale::count());
    }

    public function test_deleting_sale_restores_stock(): void
    {
        $tenant = $this->readyTenant();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $sale = Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 4,
            'unit_price' => 10,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);
        $product->update(['stock_quantity' => 6]);

        $this->actingAs($tenant, 'tenant')
            ->delete(route('tenant.sales.destroy', $sale))
            ->assertRedirect();

        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
    }
}
