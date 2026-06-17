<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTurnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $turnstile = app(TurnstileService::class);

        if (! $turnstile->isEnabled()) {
            return;
        }

        if (! $turnstile->verify(is_string($value) ? $value : null, request()->ip())) {
            $fail(__('auth.turnstile_failed'));
        }
    }
}
