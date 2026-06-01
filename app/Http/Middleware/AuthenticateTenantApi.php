<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\TenantApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTenantApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->bearerToken() ?? $request->header('X-Api-Token');
        if (! $header) {
            return response()->json(['message' => 'Token de API ausente.'], 401);
        }

        $token = TenantApiToken::findByPlainToken($header);
        if (! $token) {
            return response()->json(['message' => 'Token inválido ou expirado.'], 401);
        }

        $tenant = Tenant::query()->find($token->tenant_id);
        if (! $tenant || ! $tenant->is_active) {
            return response()->json(['message' => 'Conta inativa.'], 403);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        Auth::shouldUse('tenant');
        Auth::guard('tenant')->setUser($tenant);
        app()->instance('currentTenant', $tenant);
        app()->instance('tenant.scope.required', true);

        return $next($request);
    }
}
