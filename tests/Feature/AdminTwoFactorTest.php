<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TotpService;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class AdminTwoFactorTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_admin_without_2fa_is_redirected_to_enrollment(): void
    {
        $admin = User::factory()->superadmin()->create([
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('profile.two-factor'));
    }

    public function test_admin_with_2fa_must_complete_challenge_on_login(): void
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecret();

        User::factory()->superadmin()->create([
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
        ])->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->post('/entrar', [
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
        ])->assertRedirect(route('two-factor.challenge'));
    }

    public function test_admin_with_2fa_can_access_panel_after_challenge(): void
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecret();
        $code = $totp->getCode($secret);

        $admin = User::factory()->superadmin()->create([
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
        ]);
        $admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->post('/entrar', [
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
        ])->assertRedirect(route('two-factor.challenge'));

        $this->post(route('two-factor.challenge'), ['code' => $code])
            ->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))->assertOk();
    }
}
