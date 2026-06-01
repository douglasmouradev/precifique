<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\LGPDService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('tenant.profile-setup', [
            'tenant' => $tenant,
            'selectedNiche' => old('niche', $tenant->niche instanceof \App\Enums\NicheType ? $tenant->niche->value : (string) $tenant->niche),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();

        $data = $request->validate([
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
            'niche_other' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $interface = $data['niche'] === 'outro' ? 'artesanato' : $data['niche'];

        $tenant->update([
            'name' => $data['name'],
            'niche' => $data['niche'],
            'interface_mode' => $interface,
            'niche_metadata' => $data['niche_other'] ? ['other' => $data['niche_other']] : $tenant->niche_metadata,
            'profile_setup_completed' => true,
        ]);

        return redirect()->route('tenant.dashboard')
            ->with('success', 'Perfil configurado! Bem-vindo ao Precifique.');
    }

    public function edit(): View
    {
        return $this->show();
    }

    public function update(Request $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();

        $data = $request->validate([
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
            'niche_other' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $interface = $data['niche'] === 'outro' ? 'artesanato' : $data['niche'];

        $tenant->update([
            'name' => $data['name'],
            'niche' => $data['niche'],
            'interface_mode' => $interface,
            'niche_metadata' => $data['niche_other'] ? ['other' => $data['niche_other']] : $tenant->niche_metadata,
            'profile_setup_completed' => true,
        ]);

        return redirect()->route('tenant.dashboard')
            ->with('success', 'Perfil atualizado com sucesso.');
    }
}
