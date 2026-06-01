<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\FixedCost;
use App\Models\Product;
use App\Models\TechnicalSheet;
use App\Models\Tenant;
use App\Services\PricingCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_final_price_with_margin(): void
    {
        $tenant = Tenant::factory()->create();
        FixedCost::create([
            'tenant_id' => $tenant->id,
            'name' => 'Aluguel',
            'amount' => 1000,
            'is_active' => true,
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Bolo',
            'niche_type' => 'alimentos',
            'is_active' => true,
        ]);

        TechnicalSheet::create([
            'product_id' => $product->id,
            'material_name' => 'Farinha',
            'quantity' => 1,
            'unit' => 'kg',
            'unit_cost' => 5,
        ]);

        $service = new PricingCalculatorService;
        $result = $service->calculate($product->fresh(['technicalSheets', 'tenant']), 50);

        $this->assertEquals(5.0, $result['materials_cost']);
        $this->assertEquals(1000.0, $result['fixed_cost_share']);
        $this->assertEquals(1507.5, $result['final_price']);
        $this->assertEquals(50, $result['profit_margin_pct']);
    }
}
