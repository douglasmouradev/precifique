<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Auth::guard('tenant')->user();

        if (! $tenant) {
            $tenant = Auth::guard('tenant_member')->user()?->tenant;
        }

        if ($tenant && ! $tenant->hasVerifiedEmail() && ! $request->routeIs(
            'tenant.verification.*',
            'tenant.logout',
            'lgpd.*',
            'onboarding.*',
            'tenant.profile.setup',
            'tenant.profile.setup.store',
        )) {
            return redirect()->route('tenant.verification.notice');
        }

        return $next($request);
    }
}
