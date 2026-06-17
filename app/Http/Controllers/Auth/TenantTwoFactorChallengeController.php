<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantTwoFactorChallengeController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session('tenant_login_two_factor_id')) {
            return redirect()->route('tenant.login');
        }

        return view('auth.tenant-two-factor-challenge');
    }

    public function store(Request $request, TotpService $totp): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $tenantId = session('tenant_login_two_factor_id');
        $tenant = Tenant::find($tenantId);

        if (! $tenant || ! $tenant->two_factor_secret || ! $totp->verify((string) $tenant->two_factor_secret, $request->input('code'))) {
            return back()->withErrors(['code' => __('auth.two_factor.invalid_code')]);
        }

        session()->forget('tenant_login_two_factor_id');
        Auth::guard('tenant')->login($tenant, session('tenant_login_remember', false));
        $request->session()->regenerate();
        session()->forget('tenant_login_remember');
        session(['tenant_two_factor_verified_at' => now()->timestamp]);

        $tenant->ensureTestEmailVerified();

        return redirect()->intended(route('tenant.dashboard'));
    }
}
