<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs(
            'tenant.two-factor.*',
            'tenant.verification.*',
            'tenant.logout',
            'lgpd.*',
            'onboarding.*',
        )) {
            return $next($request);
        }

        $tenant = Auth::guard('tenant')->user();

        if ($tenant?->hasTwoFactorEnabled() && ! session('tenant_two_factor_verified_at')) {
            if (! session('tenant_login_two_factor_id')) {
                session(['tenant_login_two_factor_id' => $tenant->id]);
            }

            return redirect()->route('tenant.two-factor.challenge');
        }

        return $next($request);
    }
}
