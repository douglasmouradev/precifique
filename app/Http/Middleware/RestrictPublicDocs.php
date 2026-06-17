<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictPublicDocs
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('security.public_api_docs')) {
            abort(404);
        }

        return $next($request);
    }
}
