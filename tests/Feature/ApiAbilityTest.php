<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TenantApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class ApiAbilityTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_dashboard_requires_dashboard_read_ability(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);
        $plain = TenantApiToken::issue($tenant, 'limited', ['tokens:read']);

        $this->withToken($plain)
            ->getJson('/api/v1/dashboard/summary')
            ->assertForbidden()
            ->assertJsonPath('message', 'Permissão insuficiente para este recurso.');
    }

    public function test_token_revoke_denied_for_other_tenant_token(): void
    {
        $tenantA = $this->readyTenant(['email' => 'a@test.com']);
        $tenantB = $this->readyTenant(['email' => 'b@test.com']);

        $plainA = TenantApiToken::issue($tenantA, 'a', ['dashboard:read', 'tokens:read', 'tokens:write']);
        TenantApiToken::issue($tenantB, 'b', ['dashboard:read', 'tokens:read', 'tokens:write']);
        $tokenBId = TenantApiToken::query()->where('tenant_id', $tenantB->id)->value('id');

        $this->withToken($plainA)
            ->deleteJson("/api/v1/auth/tokens/{$tokenBId}")
            ->assertNotFound();
    }
}
