<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\UnifiedLoginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('tenant.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request, UnifiedLoginService $login): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $result = $login->attempt($request, $credentials);

        if (! $result->successful) {
            RateLimiter::hit($request->throttleKey());

            return $result->response;
        }

        RateLimiter::clear($request->throttleKey());

        return $result->response;
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
