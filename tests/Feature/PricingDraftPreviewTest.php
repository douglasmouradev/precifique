<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class PricingDraftPreviewTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_preview_uses_draft_materials_without_saving(): void
    {
        $tenant = $this->readyTenant(['plan' => 'basic']);
        $product = Product::factory()->for($tenant)->create();

        $response = $this->actingAs($tenant, 'tenant')
            ->postJson(route('tenant.pricing.preview', $product), [
                'profit_margin_percent' => 50,
                'materials' => [
                    ['material_name' => 'Chocolate', 'quantity' => 2, 'unit_cost' => 10],
                ],
                'hourly_rate' => 0,
                'hours_spent' => 0,
            ]);

        $response->assertOk();
        $breakdown = $response->json('breakdown');
        $this->assertGreaterThanOrEqual(20, $breakdown['materials_cost']);
        $this->assertEquals(50, $breakdown['profit_margin_pct']);
    }
}
