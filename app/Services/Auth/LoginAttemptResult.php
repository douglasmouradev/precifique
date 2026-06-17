<?php

declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Http\RedirectResponse;

final class LoginAttemptResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly RedirectResponse $response,
    ) {}
}
