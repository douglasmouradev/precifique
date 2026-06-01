<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\FixedCost;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class TenantResourceIsolationTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_cannot_update_other_tenant_fixed_cost(): void
    {
        $tenantA = $this->readyTenant();
        $tenantB = $this->readyTenant();

        $cost = FixedCost::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Aluguel',
            'amount' => 1000,
            'is_active' => true,
        ]);

        $this->actingAs($tenantA, 'tenant')
            ->put(route('tenant.fixed-costs.update', $cost), [
                'name' => 'Hack',
                'amount' => 1,
                'is_active' => true,
            ])
            ->assertNotFound();

        $this->assertSame('Aluguel', $cost->fresh()->name);
    }

    public function test_tenant_cannot_delete_other_tenant_sale(): void
    {
        $tenantA = $this->readyTenant();
        $tenantB = $this->readyTenant();

        $product = Product::factory()->create(['tenant_id' => $tenantB->id]);
        $sale = Sale::create([
            'tenant_id' => $tenantB->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);

        $this->actingAs($tenantA, 'tenant')
            ->delete(route('tenant.sales.destroy', $sale))
            ->assertNotFound();

        $this->assertDatabaseHas('sales', ['id' => $sale->id]);
    }
}
