<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantApiToken;
use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class TenantApiReadyTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_api_rejects_tenant_without_onboarding(): void
    {
        $tenant = $this->readyTenant([
            'onboarding_completed' => false,
            'profile_setup_completed' => true,
        ]);
        $plain = TenantApiToken::issue($tenant, 'test');

        $this->withToken($plain)
            ->getJson('/api/v1/dashboard/summary')
            ->assertForbidden()
            ->assertJsonPath('message', 'Complete o onboarding no painel web.');
    }

    public function test_api_rejects_tenant_without_lgpd(): void
    {
        $tenant = Tenant::factory()->create([
            'profile_setup_completed' => true,
            'onboarding_completed' => true,
        ]);
        $plain = TenantApiToken::issue($tenant, 'test');

        $this->withToken($plain)
            ->getJson('/api/v1/dashboard/summary')
            ->assertForbidden()
            ->assertJsonPath('message', 'Aceite os termos LGPD no painel web antes de usar a API.');
    }

    public function test_api_can_revoke_token(): void
    {
        $tenant = $this->readyTenant(['plan' => 'premium']);
        $plain = TenantApiToken::issue($tenant, 'revoke-me', ['dashboard:read', 'tokens:read', 'tokens:write']);
        $tokenId = TenantApiToken::query()->where('tenant_id', $tenant->id)->value('id');

        $this->withToken($plain)
            ->deleteJson("/api/v1/auth/tokens/{$tokenId}")
            ->assertOk();

        $this->withToken($plain)
            ->getJson('/api/v1/dashboard/summary')
            ->assertUnauthorized();
    }
}
