<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\TenantApiToken;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ApiSalesStoreTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_api_can_create_sale(): void
    {
        $tenant = $this->readyTenant();
        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'stock_quantity' => 10,
        ]);

        $plain = TenantApiToken::issue($tenant, 'test', ['sales:write']);

        $this->withToken($plain)
            ->postJson('/api/v1/sales', [
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => 25.50,
                'payment_method' => 'pix',
            ])
            ->assertCreated()
            ->assertJsonPath('quantity', 2);

        $this->assertSame(8, $product->fresh()->stock_quantity);
    }
}
