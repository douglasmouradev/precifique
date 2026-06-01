<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class TenantPlanLimitTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_basic_plan_limits_products_to_five(): void
    {
        $tenant = $this->readyTenant(['plan' => 'basic']);

        for ($i = 0; $i < 5; $i++) {
            Product::create([
                'tenant_id' => $tenant->id,
                'name' => "Produto {$i}",
                'niche_type' => 'alimentos',
            ]);
        }

        $response = $this->actingAs($tenant, 'tenant')->get(route('tenant.products.create'));

        $response->assertRedirect(route('tenant.billing.upgrade'));
    }
}
