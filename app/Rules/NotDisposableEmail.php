<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotDisposableEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! str_contains($value, '@')) {
            return;
        }

        $domain = strtolower((string) substr(strrchr($value, '@'), 1));
        $blocked = config('security.disposable_email_domains', []);

        if (in_array($domain, $blocked, true)) {
            $fail(__('auth.register.disposable_email'));
        }
    }
}
