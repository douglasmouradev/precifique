<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\Concerns\AuthorizesTenantResource;
use App\Http\Controllers\Controller;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    use AuthorizesTenantResource;

    public function show(TotpService $totp): View
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant();
        $secret = $tenant->two_factor_secret;

        if (! $secret) {
            $secret = $totp->generateSecret();
            $tenant->forceFill(['two_factor_secret' => $secret])->save();
        }

        return view('tenant.two-factor', [
            'qrUri' => $totp->getQrUri($secret, $tenant->email),
            'secret' => $secret,
            'enabled' => $tenant->hasTwoFactorEnabled(),
        ]);
    }

    public function confirm(Request $request, TotpService $totp): RedirectResponse
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant();
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        if (! $tenant->two_factor_secret || ! $totp->verify((string) $tenant->two_factor_secret, $request->input('code'))) {
            return back()->withErrors(['code' => __('auth.two_factor.invalid_code')]);
        }

        $tenant->forceFill(['two_factor_confirmed_at' => now()])->save();
        session(['tenant_two_factor_verified_at' => now()->timestamp]);

        return back()->with('success', __('Two-factor authentication enabled.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->authorizeTenantOwner();

        $request->validate(['password' => ['required', 'current_password:tenant']]);

        $tenant = current_tenant();
        $tenant->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
        session()->forget('tenant_two_factor_verified_at');

        return back()->with('success', __('Two-factor authentication disabled.'));
    }
}
