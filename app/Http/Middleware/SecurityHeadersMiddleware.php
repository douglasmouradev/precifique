<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        if (config('security.csp')) {
            $nonce = $request->attributes->get('csp_nonce');
            $policy = (string) config('security.csp_policy');
            if (is_string($nonce) && $nonce !== '') {
                $policy = str_replace('{nonce}', $nonce, $policy);
            }
            $response->headers->set('Content-Security-Policy', $policy);
        }

        if (config('security.hsts') && $request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age='.config('security.hsts_max_age').'; includeSubDomains'
            );
        }

        return $response;
    }
}
