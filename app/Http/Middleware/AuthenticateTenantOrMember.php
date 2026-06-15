<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTenantOrMember
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('tenant')->check() && ! Auth::guard('tenant_member')->check()) {
            return redirect()->route('tenant.login');
        }

        return $next($request);
    }
}
