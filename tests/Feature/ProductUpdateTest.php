<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ProductUpdateTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_can_update_product_details(): void
    {
        $tenant = $this->readyTenant();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Bolo antigo',
            'stock_quantity' => 5,
        ]);

        $this->actingAs($tenant, 'tenant')
            ->put(route('tenant.products.update', $product), [
                'name' => 'Bolo novo',
                'description' => 'Descrição atualizada',
                'niche_type' => 'alimentos',
                'stock_quantity' => 12,
                'min_stock_alert' => 2,
                'is_active' => 1,
            ])
            ->assertRedirect(route('tenant.products.index'));

        $product->refresh();
        $this->assertSame('Bolo novo', $product->name);
        $this->assertSame(12, $product->stock_quantity);
    }

    public function test_tenant_cannot_update_other_tenant_product(): void
    {
        $tenantA = $this->readyTenant(['email' => 'a@test.com']);
        $tenantB = $this->readyTenant(['email' => 'b@test.com']);
        $product = Product::factory()->create(['tenant_id' => $tenantB->id]);

        $this->actingAs($tenantA, 'tenant')
            ->put(route('tenant.products.update', $product), [
                'name' => 'Hack',
                'niche_type' => 'alimentos',
            ])
            ->assertNotFound();
    }
}
