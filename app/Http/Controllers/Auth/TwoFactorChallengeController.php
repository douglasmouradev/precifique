<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TotpService;
use App\Services\TwoFactorRecoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session('login.two_factor_user_id')) {
            return redirect()->route('tenant.login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TotpService $totp, TwoFactorRecoveryService $recovery): RedirectResponse
    {
        $userId = session('login.two_factor_user_id');
        if (! $userId) {
            return redirect()->route('tenant.login');
        }

        $request->validate([
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        $user = User::query()->find($userId);
        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return redirect()->route('tenant.login');
        }

        $code = (string) $request->input('code', '');
        $recoveryCode = (string) $request->input('recovery_code', '');
        $verified = false;

        if ($recoveryCode !== '') {
            $verified = $recovery->consume($user, $recoveryCode);
        } elseif (preg_match('/^\d{6}$/', $code)) {
            $verified = $totp->verify((string) $user->two_factor_secret, $code);
        }

        if (! $verified) {
            return back()->withErrors(['code' => __('auth.two_factor.invalid_code')]);
        }

        session()->forget('login.two_factor_user_id');
        Auth::login($user, (bool) session('login.remember', false));
        session()->forget('login.remember');
        $request->session()->regenerate();
        session(['two_factor_verified_at' => now()->timestamp]);

        return redirect()->intended(route('admin.dashboard'));
    }
}
