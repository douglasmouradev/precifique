<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    public function isEnabled(): bool
    {
        $secret = (string) config('security.turnstile.secret_key', '');

        return $secret !== '';
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if ($token === null || $token === '') {
            return false;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('security.turnstile.secret_key'),
            'response' => $token,
            'remoteip' => $ip,
        ]);

        if (! $response->successful()) {
            return false;
        }

        return (bool) $response->json('success');
    }
}
