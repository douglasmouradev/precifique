<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class PricingMarginValidationTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_basic_plan_cannot_use_premium_margin(): void
    {
        $tenant = $this->readyTenant(['plan' => 'basic']);
        $product = Product::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($tenant, 'tenant')
            ->put(route('tenant.pricing.update', $product), [
                'name' => $product->name,
                'profit_margin_percent' => 150,
            ])
            ->assertSessionHasErrors('profit_margin_percent');
    }

    public function test_premium_tenant_can_use_150_margin(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);
        $product = Product::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($tenant, 'tenant')
            ->put(route('tenant.pricing.update', $product), [
                'name' => $product->name,
                'profit_margin_percent' => 150,
                'stock_quantity' => 0,
                'min_stock_alert' => 5,
            ])
            ->assertRedirect(route('tenant.pricing.edit', $product));
    }
}
