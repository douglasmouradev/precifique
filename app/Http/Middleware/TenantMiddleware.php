<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LGPDService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function __construct(
        private readonly LGPDService $lgpdService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Auth::guard('tenant')->user();

        if (! $tenant) {
            $member = Auth::guard('tenant_member')->user();
            $tenant = $member?->tenant;
            if ($member && (! $member->is_active || ! $tenant?->is_active)) {
                return redirect()->route('tenant.login')
                    ->withErrors(['email' => 'Conta inativa ou não encontrada.']);
            }
        }

        if (! $tenant || ! $tenant->is_active) {
            return redirect()->route('tenant.login')
                ->withErrors(['email' => 'Conta inativa ou não encontrada.']);
        }

        App::instance('currentTenant', $tenant);
        App::instance('tenant.scope.required', true);

        if (! $this->lgpdService->hasRequiredConsents($tenant)) {
            return redirect()->route('lgpd.consent');
        }

        if (! $tenant->profile_setup_completed && ! $request->routeIs('tenant.profile.*')) {
            return redirect()->route('tenant.profile.setup');
        }

        if (! $tenant->onboarding_completed && ! $request->routeIs('onboarding.*')) {
            return redirect()->route('onboarding.welcome');
        }

        return $next($request);
    }
}
