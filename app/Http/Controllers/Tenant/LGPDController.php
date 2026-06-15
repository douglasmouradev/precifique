<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\LGPDService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LGPDController extends Controller
{
    public function __construct(
        private readonly LGPDService $lgpd,
    ) {}

    public function consentForm(): View
    {
        return view('lgpd.consent');
    }

    public function storeConsent(Request $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $version = (string) config('lgpd.policy_version', '1.0');

        $request->validate([
            'terms' => ['accepted'],
            'privacy' => ['accepted'],
        ]);

        $this->lgpd->recordConsent($tenant, $request, 'terms', $version);
        $this->lgpd->recordConsent($tenant, $request, 'privacy', $version);

        if ($request->boolean('marketing')) {
            $this->lgpd->recordConsent($tenant, $request, 'marketing', $version);
        }

        if (session()->pull('onboarding_selected_plan') === 'premium') {
            return redirect()->route('tenant.billing.upgrade')
                ->with('success', 'Finalize o pagamento para ativar o Premium.');
        }

        $tenant = Auth::guard('tenant')->user();

        if ($tenant && ! $tenant->onboarding_completed) {
            return redirect()->route('onboarding.welcome')
                ->with('success', 'Termos aceitos! Vamos configurar sua conta.');
        }

        $redirect = redirect()->route('tenant.dashboard');

        if (session()->pull('guided_setup')) {
            $redirect->with('guided_setup', true);
        }

        return $redirect;
    }

    public function portal(): View
    {
        $tenant = Auth::guard('tenant')->user();

        return view('lgpd.portal', compact('tenant'));
    }

    public function export(): StreamedResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $data = $this->lgpd->exportTenantData($tenant);

        return response()->streamDownload(
            fn () => print (json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            'precifique-dados-'.$tenant->uuid.'.json',
            ['Content-Type' => 'application/json']
        );
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => ['required', 'in:EXCLUIR'],
            'password' => ['required', 'string'],
        ]);

        $tenant = Auth::guard('tenant')->user();

        if (! Auth::guard('tenant')->validate([
            'email' => $tenant->email,
            'password' => $request->input('password'),
        ])) {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }

        $this->lgpd->anonymizeTenant($tenant);
        Auth::guard('tenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Conta anonimizada conforme LGPD.');
    }
}
