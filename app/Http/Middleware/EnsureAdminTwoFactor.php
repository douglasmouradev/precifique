<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user?->hasTwoFactorEnabled() && ! session('two_factor_verified_at')) {
            if (! session('login.two_factor_user_id')) {
                session([
                    'login.two_factor_user_id' => $user->id,
                    'login.remember' => false,
                ]);
                Auth::logout();
            }

            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
