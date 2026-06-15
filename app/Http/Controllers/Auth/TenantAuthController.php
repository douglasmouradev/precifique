<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TenantRegisterRequest;
use App\Models\Tenant;
use App\Support\TenantNicheMapper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.tenant-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('tenant')->attempt($credentials, $request->boolean('remember'))) {
            $tenant = Auth::guard('tenant')->user();

            if ($tenant?->hasTwoFactorEnabled()) {
                Auth::guard('tenant')->logout();
                $request->session()->put('tenant_login_two_factor_id', $tenant->id);
                $request->session()->put('tenant_login_remember', $request->boolean('remember'));

                return redirect()->route('tenant.two-factor.challenge');
            }

            $request->session()->regenerate();
            session(['tenant_two_factor_verified_at' => now()->timestamp]);

            return redirect()->intended(route('tenant.dashboard'));
        }

        return back()
            ->withErrors(['email' => __('auth.failed')])
            ->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.tenant-register');
    }

    public function register(TenantRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $niche = TenantNicheMapper::map($data);

        $tenant = Tenant::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'niche' => $niche['niche'],
            'interface_mode' => $niche['interface_mode'],
            'niche_metadata' => $niche['niche_metadata'],
            'plan' => 'basic',
            'trial_ends_at' => now()->addDays((int) config('tenancy.trial_days', 14)),
        ]);

        Auth::guard('tenant')->login($tenant);

        $tenant->sendEmailVerificationNotification();

        return redirect()->route('lgpd.consent');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('tenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
