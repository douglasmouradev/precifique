<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session('login.two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TotpService $totp): RedirectResponse
    {
        $userId = session('login.two_factor_user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $user = User::query()->find($userId);
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        if (! $totp->verify((string) $user->two_factor_secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Código inválido.']);
        }

        session()->forget('login.two_factor_user_id');
        Auth::login($user, (bool) session('login.remember', false));
        session()->forget('login.remember');
        $request->session()->regenerate();
        session(['two_factor_verified_at' => now()->timestamp]);

        return redirect()->intended(route('admin.dashboard'));
    }
}
