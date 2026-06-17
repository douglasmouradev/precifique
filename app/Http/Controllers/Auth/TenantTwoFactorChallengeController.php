<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantMember;
use App\Services\TotpService;
use App\Services\TwoFactorRecoveryService;
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

    public function store(Request $request, TotpService $totp, TwoFactorRecoveryService $recovery): RedirectResponse
    {
        $request->validate([
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        $tenantId = session('tenant_login_two_factor_id');
        $tenant = Tenant::find($tenantId);

        if (! $tenant || ! $tenant->hasTwoFactorEnabled()) {
            return redirect()->route('tenant.login');
        }

        $code = (string) $request->input('code', '');
        $recoveryCode = (string) $request->input('recovery_code', '');
        $verified = false;

        if ($recoveryCode !== '') {
            $verified = $recovery->consume($tenant, $recoveryCode);
        } elseif (preg_match('/^\d{6}$/', $code)) {
            $verified = $tenant->two_factor_secret && $totp->verify((string) $tenant->two_factor_secret, $code);
        }

        if (! $verified) {
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
