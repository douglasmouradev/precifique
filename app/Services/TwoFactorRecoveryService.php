<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
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
    public function store(Model $model, array $plainCodes): array
    {
        $hashed = array_map(fn (string $code) => hash('sha256', str_replace('-', '', strtolower($code))), $plainCodes);
        $model->forceFill(['two_factor_recovery_codes' => $hashed])->save();

        return $plainCodes;
    }

    public function consume(Model $model, string $code): bool
    {
        $normalized = hash('sha256', str_replace(['-', ' '], '', strtolower($code)));
        $stored = $model->two_factor_recovery_codes;

        if (! is_array($stored) || $stored === []) {
            return false;
        }

        $index = array_search($normalized, $stored, true);
        if ($index === false) {
            return false;
        }

        unset($stored[$index]);
        $model->forceFill(['two_factor_recovery_codes' => array_values($stored)])->save();

        return true;
    }
}
