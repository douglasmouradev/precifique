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

        if (! app()->environment(['local', 'testing'])) {
            if ($token === null || $token === '') {
                abort(503, 'Health check não configurado.');
            }

            if ($request->bearerToken() !== $token) {
                abort(403, 'Health check token inválido.');
            }

            return $next($request);
        }

        if ($token !== null && $token !== '' && $request->bearerToken() !== $token) {
            abort(403, 'Health check token inválido.');
        }

        return $next($request);
    }
}
