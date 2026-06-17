<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminTwoFactorEnrolled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if (! $user?->is_superadmin || $user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        return redirect()
            ->route('profile.two-factor')
            ->with('warning', __('auth.two_factor.admin_required'));
    }
}
