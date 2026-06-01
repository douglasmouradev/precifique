<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
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
            $request->session()->regenerate();

            return redirect()->intended(route('tenant.dashboard'));
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.tenant-register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
        ]);

        $tenant = Tenant::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'niche' => $data['niche'],
            'interface_mode' => $data['niche'] === 'outro' ? 'artesanato' : $data['niche'],
            'plan' => 'basic',
            'trial_ends_at' => now()->addDays((int) config('tenancy.trial_days', 14)),
        ]);

        Auth::guard('tenant')->login($tenant);

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
