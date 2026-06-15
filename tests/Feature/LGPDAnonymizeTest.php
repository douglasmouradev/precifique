<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Tenant;
use App\Services\LGPDService;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class LGPDAnonymizeTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymize_removes_tenant_data(): void
    {
        $tenant = Tenant::factory()->create();
        $product = Product::factory()->create(['tenant_id' => $tenant->id]);
        Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10,
            'total_amount' => 10,
            'payment_method' => 'pix',
            'sold_at' => now(),
        ]);

        app(LGPDService::class)->anonymizeTenant($tenant);

        $this->assertSoftDeleted('tenants', ['id' => $tenant->id]);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('sales', ['tenant_id' => $tenant->id]);
    }
}
