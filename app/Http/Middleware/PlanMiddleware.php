<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PlanMiddleware
{
    public function handle(Request $request, Closure $next, string $requiredPlan = 'premium'): Response
    {
        $tenant = Auth::guard('tenant')->user();

        if (! $tenant) {
            return redirect()->route('tenant.login');
        }

        if ($requiredPlan === 'premium' && ! $tenant->isPremium()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Este recurso está disponível no plano Premium.',
                ], 403);
            }

            return redirect()
                ->route('tenant.billing.upgrade')
                ->with('warning', 'Este recurso está disponível no plano Premium.');
        }

        return $next($request);
    }
}
