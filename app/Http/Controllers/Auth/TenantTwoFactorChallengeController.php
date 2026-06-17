<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantMember;
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
        $memberId = session()->pull('tenant_login_member_id');
        $remember = session()->pull('tenant_login_remember', false);

        if ($memberId) {
            $member = TenantMember::query()->find($memberId);
            if (! $member || $member->tenant_id !== $tenant->id || ! $member->is_active) {
                return redirect()->route('tenant.login')
                    ->withErrors(['email' => __('auth.failed')]);
            }

            Auth::guard('tenant_member')->login($member, $remember);
        } else {
            Auth::guard('tenant')->login($tenant, $remember);
            $tenant->ensureTestEmailVerified();
        }

        $request->session()->regenerate();
        session(['tenant_two_factor_verified_at' => now()->timestamp]);

        return redirect()->intended(route('tenant.dashboard'));
    }
}
