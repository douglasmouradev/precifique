<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\Concerns\AuthorizesTenantResource;
use App\Http\Controllers\Controller;
use App\Services\LGPDService;
use App\Support\ForgetsTenantSetupProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LGPDController extends Controller
{
    use AuthorizesTenantResource;
    use ForgetsTenantSetupProgress;

    public function __construct(
        private readonly LGPDService $lgpd,
    ) {}

    public function consentForm(): View
    {
        return view('lgpd.consent');
    }

    public function storeConsent(Request $request): RedirectResponse
    {
        $tenant = current_tenant();
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

        $this->forgetTenantSetupProgress($tenant);

        if (session()->pull('onboarding_selected_plan') === 'premium') {
            return redirect()->route('tenant.billing.upgrade')
                ->with('success', __('lgpd.portal.finish_premium_payment'));
        }

        $tenant = current_tenant();

        if ($tenant && ! $tenant->onboarding_completed) {
            return redirect()->route('onboarding.welcome')
                ->with('success', __('lgpd.portal.terms_accepted'));
        }

        $redirect = redirect()->route('tenant.dashboard');

        if (session()->pull('guided_setup')) {
            $redirect->with('guided_setup', true);
        }

        return $redirect;
    }

    public function portal(): View
    {
        $tenant = current_tenant();

        return view('lgpd.portal', compact('tenant'));
    }

    public function export(): StreamedResponse
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant();
        $data = $this->lgpd->exportTenantData($tenant);

        return response()->streamDownload(
            fn () => print (json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            'precifique-dados-'.$tenant->uuid.'.json',
            ['Content-Type' => 'application/json']
        );
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $this->authorizeTenantOwner();

        $request->validate([
            'confirm' => ['required', 'in:EXCLUIR,DELETE'],
            'password' => ['required', 'string'],
        ]);

        $tenant = current_tenant();

        if (! Auth::guard('tenant')->validate([
            'email' => $tenant->email,
            'password' => $request->input('password'),
        ])) {
            return back()->withErrors(['password' => __('lgpd.portal.wrong_password')]);
        }

        $this->lgpd->anonymizeTenant($tenant);
        Auth::guard('tenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', __('lgpd.portal.account_anonymized'));
    }
}
