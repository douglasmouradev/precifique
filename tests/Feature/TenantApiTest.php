<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TenantApiToken;
use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class TenantApiTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_can_obtain_api_token(): void
    {
        $tenant = $this->readyTenant([
            'email' => 'api@demo.com',
            'password' => 'secret1234',
            'plan' => 'premium',
        ]);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $tenant->email,
            'password' => 'secret1234',
            'device_name' => 'test',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'tenant']);
    }

    public function test_api_token_can_access_dashboard_summary(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);
        $plain = TenantApiToken::issue($tenant, 'phpunit');

        $response = $this->withToken($plain)
            ->getJson('/api/v1/dashboard/summary');

        $response->assertOk()
            ->assertJsonStructure(['month_revenue', 'sales_count', 'goal_amount', 'goal_progress', 'products_count']);
    }

    public function test_invalid_api_token_is_rejected(): void
    {
        $this->getJson('/api/v1/dashboard/summary', [
            'Authorization' => 'Bearer invalid-token',
        ])->assertUnauthorized();
    }
}
