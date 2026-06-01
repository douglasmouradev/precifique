<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantRouteIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_cannot_access_other_tenant_product(): void
    {
        $tenantA = Tenant::factory()->create(['onboarding_completed' => true]);
        $tenantB = Tenant::factory()->create(['onboarding_completed' => true]);

        foreach ([$tenantA, $tenantB] as $tenant) {
            foreach (['terms', 'privacy'] as $type) {
                $tenant->lgpdConsents()->create([
                    'consent_type' => $type,
                    'consented_at' => now(),
                    'ip_address' => '127.0.0.1',
                    'version' => '1.0',
                ]);
            }
        }

        $productB = Product::factory()->create(['tenant_id' => $tenantB->id]);

        $this->actingAs($tenantA, 'tenant')
            ->get(route('tenant.pricing.edit', $productB))
            ->assertNotFound();
    }
}
