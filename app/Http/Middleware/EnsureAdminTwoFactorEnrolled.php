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
        $user = Auth::user();

        if ($user?->is_superadmin && ! $user->hasTwoFactorEnabled()) {
            if ($request->routeIs('admin.two-factor.*')) {
                return $next($request);
            }

            return redirect()->route('admin.two-factor.show')
                ->with('warning', __('auth.two_factor.admin_required'));
        }

        return $next($request);
    }
}
