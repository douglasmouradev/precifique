<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        if ($user?->hasTwoFactorEnabled()) {
            $remember = $request->boolean('remember');
            Auth::logout();
            $request->session()->put('login.two_factor_user_id', $user->id);
            $request->session()->put('login.remember', $remember);

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        if ($user?->is_superadmin) {
            session(['two_factor_verified_at' => now()->timestamp]);

            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->forget('two_factor_verified_at');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
