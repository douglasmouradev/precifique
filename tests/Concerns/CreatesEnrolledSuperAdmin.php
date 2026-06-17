<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\User;
use App\Services\TotpService;

trait CreatesEnrolledSuperAdmin
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function enrolledSuperAdmin(array $attributes = []): User
    {
        $secret = app(TotpService::class)->generateSecret();

        $admin = User::factory()->superadmin()->create($attributes);
        $admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $admin;
    }

    protected function actingAsEnrolledSuperAdmin(User $admin): static
    {
        return $this->withSession(['two_factor_verified_at' => now()->timestamp])
            ->actingAs($admin);
    }
}
