<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\TenantApiToken;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ApiProductsTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_api_lists_tenant_products(): void
    {
        $tenant = $this->readyTenant();
        Product::factory()->count(2)->create(['tenant_id' => $tenant->id]);
        $plain = TenantApiToken::issue($tenant, 'test', ['products:read']);

        $this->withToken($plain)
            ->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
