<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class SalesIndexTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_sales_index_loads_with_existing_sales(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);
        $product = Product::factory()->create(['tenant_id' => $tenant->id]);

        Sale::factory()->create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 18.50,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);

        $this->actingAs($tenant, 'tenant')
            ->get(route('tenant.sales.index'))
            ->assertOk()
            ->assertSee('R$ 37,00');
    }
}
