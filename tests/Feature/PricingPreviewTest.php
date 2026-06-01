<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class PricingPreviewTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_preview_returns_breakdown_for_valid_margin(): void
    {
        $tenant = $this->readyTenant(['plan' => 'basic']);
        $product = Product::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($tenant, 'tenant')
            ->postJson(route('tenant.pricing.preview', $product), [
                'profit_margin_percent' => 50,
            ]);

        $response->assertOk()
            ->assertJsonStructure([
                'breakdown' => [
                    'total_production',
                    'profit_absolute',
                    'final_price',
                    'profit_margin_pct',
                ],
            ]);

        $breakdown = $response->json('breakdown');
        $this->assertEquals(50, $breakdown['profit_margin_pct']);
        $this->assertGreaterThanOrEqual(0, $breakdown['final_price']);
    }

    public function test_preview_rejects_invalid_margin_for_basic_plan(): void
    {
        $tenant = $this->readyTenant(['plan' => 'basic']);
        $product = Product::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($tenant, 'tenant')
            ->postJson(route('tenant.pricing.preview', $product), [
                'profit_margin_percent' => 150,
            ])
            ->assertStatus(422);
    }
}
