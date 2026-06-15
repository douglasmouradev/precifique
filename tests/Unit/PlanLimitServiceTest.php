<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Tenant;
use App\Services\PlanLimitService;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class PlanLimitServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_plan_blocks_sixth_product(): void
    {
        $tenant = Tenant::factory()->create(['plan' => 'basic']);
        $service = app(PlanLimitService::class);

        for ($i = 0; $i < 5; $i++) {
            $tenant->products()->create([
                'name' => "Produto {$i}",
                'niche_type' => 'alimentos',
            ]);
        }

        $this->assertFalse($service->canCreateProduct($tenant->fresh()));
        $this->assertSame(5, $service->maxProducts($tenant));
    }

    public function test_premium_plan_has_unlimited_products(): void
    {
        $tenant = Tenant::factory()->create(['plan' => 'premium']);
        $service = app(PlanLimitService::class);

        $this->assertTrue($service->canCreateProduct($tenant));
        $this->assertNull($service->maxProducts($tenant));
    }

    public function test_basic_plan_margin_150_not_allowed(): void
    {
        $tenant = Tenant::factory()->create(['plan' => 'basic']);
        $service = app(PlanLimitService::class);

        $this->assertFalse($service->isMarginAllowed($tenant, 150.0));
        $this->assertTrue($service->isMarginAllowed($tenant, 50.0));
    }
}
