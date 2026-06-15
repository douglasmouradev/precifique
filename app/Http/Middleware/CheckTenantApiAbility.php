<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\TenantApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantApiAbility
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $token = $request->attributes->get('tenant_api_token');

        if (! $token instanceof TenantApiToken || ! $token->hasAbility($ability)) {
            return response()->json(['message' => 'Permissão insuficiente para este recurso.'], 403);
        }

        return $next($request);
    }
}
