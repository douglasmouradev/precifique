<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\PlanType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TenantRegisterRequest;
use App\Models\Tenant;
use App\Services\Auth\UnifiedLoginService;
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

    public function login(Request $request, UnifiedLoginService $login): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        return $login->attempt($request, $credentials)->response;
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
            'trial_ends_at' => now()->addDays((int) config('tenancy.trial_days', 14)),
        ]);
        $tenant->forceFill(['plan' => PlanType::Basic])->save();

        Auth::guard('tenant')->login($tenant);

        $tenant->sendEmailVerificationNotification();

        return redirect()->route('lgpd.consent');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('tenant')->logout();
        Auth::guard('tenant_member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
