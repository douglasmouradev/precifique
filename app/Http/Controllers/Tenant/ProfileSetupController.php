<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreProfileSetupRequest;
use App\Services\LGPDService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileSetupController extends Controller
{
    public function __construct(
        private readonly LGPDService $lgpdService,
    ) {}

    public function show(): View|RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();

        if (! $this->lgpdService->hasRequiredConsents($tenant)) {
            return redirect()->route('lgpd.consent');
        }

        if (! $tenant->onboarding_completed) {
            return redirect()->route('onboarding.welcome');
        }

        return view('tenant.profile-setup', [
            'tenant' => $tenant,
            'selectedNiche' => old('niche', $tenant->niche?->value ?? (string) $tenant->niche),
        ]);
    }

    public function store(StoreProfileSetupRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $tenant->update($request->profileAttributes());

        return redirect()->route('tenant.dashboard')
            ->with('success', 'Perfil configurado! Bem-vindo ao Precifique.');
    }

    public function edit(): View
    {
        return $this->show();
    }

    public function update(StoreProfileSetupRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $tenant->update($request->profileAttributes());

        return redirect()->route('tenant.dashboard')
            ->with('success', 'Perfil atualizado com sucesso.');
    }
}
