<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class TwoFactorRecoveryService
{
    public function generateSet(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4).'-'.Str::random(4));
        }

        return $codes;
    }

    /**
     * @return array<int, string>
     */
    public function store(User $user, array $plainCodes): array
    {
        $hashed = array_map(fn (string $code) => hash('sha256', str_replace('-', '', strtolower($code))), $plainCodes);
        $user->forceFill(['two_factor_recovery_codes' => $hashed])->save();

        return $plainCodes;
    }

    public function consume(User $user, string $code): bool
    {
        $normalized = hash('sha256', str_replace(['-', ' '], '', strtolower($code)));
        $stored = $user->two_factor_recovery_codes;

        if (! is_array($stored) || $stored === []) {
            return false;
        }

        $index = array_search($normalized, $stored, true);
        if ($index === false) {
            return false;
        }

        unset($stored[$index]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($stored)])->save();

        return true;
    }
}
