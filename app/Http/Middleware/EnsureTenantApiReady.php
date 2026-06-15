<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\LGPDService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantApiReady
{
    public function __construct(
        private readonly LGPDService $lgpdService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Auth::guard('tenant')->user();

        if (! $tenant) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        if (! $this->lgpdService->hasRequiredConsents($tenant)) {
            return response()->json(['message' => 'Aceite os termos LGPD no painel web antes de usar a API.'], 403);
        }

        if (! $tenant->profile_setup_completed) {
            return response()->json(['message' => 'Complete a configuração do perfil no painel web.'], 403);
        }

        if (! $tenant->onboarding_completed) {
            return response()->json(['message' => 'Complete o onboarding no painel web.'], 403);
        }

        if (! $tenant->hasVerifiedEmail()) {
            return response()->json(['message' => 'Verifique seu e-mail no painel web antes de usar a API.'], 403);
        }

        return $next($request);
    }
}
