<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHealthCheckToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('precifique.monitoring.health_token');

        if ($token === null || $token === '') {
            return $next($request);
        }

        if ($request->bearerToken() !== $token) {
            abort(403, 'Health check token inválido.');
        }

        return $next($request);
    }
}
