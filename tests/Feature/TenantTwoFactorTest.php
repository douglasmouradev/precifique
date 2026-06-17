<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Services\TotpService;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class TenantTwoFactorTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_with_2fa_is_redirected_to_challenge_and_can_complete_login(): void
    {
        $secret = app(TotpService::class)->generateSecret();
        $tenant = $this->readyTenant([
            'email' => '2fa@precifique.com.br',
            'password' => 'demo1234',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->post('/entrar', [
            'email' => '2fa@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.two-factor.challenge'));

        $this->assertGuest('tenant');

        $code = app(TotpService::class)->getCode($secret);

        $this->post('/auth/2fa', ['code' => $code])
            ->assertRedirect(route('tenant.dashboard'));

        $this->assertAuthenticatedAs($tenant, 'tenant');
    }

    public function test_demo_profile_skips_2fa_even_when_enabled(): void
    {
        $secret = app(TotpService::class)->generateSecret();
        $tenant = $this->readyTenant([
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->post('/entrar', [
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.dashboard'));

        $this->assertAuthenticatedAs($tenant, 'tenant');
    }
}
