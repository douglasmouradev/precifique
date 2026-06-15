<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_2fa_is_redirected_to_challenge(): void
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecret();

        $admin = User::factory()->create([
            'is_superadmin' => true,
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('two-factor.challenge'));
    }

    public function test_admin_with_verified_2fa_session_can_access_dashboard(): void
    {
        $totp = app(TotpService::class);
        $secret = $totp->generateSecret();

        $admin = User::factory()->create([
            'is_superadmin' => true,
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->withSession(['two_factor_verified_at' => now()->timestamp])
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}
