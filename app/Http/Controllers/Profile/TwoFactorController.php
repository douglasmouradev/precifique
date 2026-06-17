<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function show(TotpService $totp): View|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user?->is_superadmin, 403);

        $secret = $user->two_factor_secret;

        if (! $secret) {
            $secret = $totp->generateSecret();
            $user->forceFill(['two_factor_secret' => $secret])->save();
        }

        return view('profile.two-factor', [
            'qrUri' => $totp->getQrUri($secret, $user->email),
            'enabled' => $user->hasTwoFactorEnabled(),
        ]);
    }

    public function confirm(Request $request, TotpService $totp): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user?->is_superadmin, 403);

        $request->validate(['code' => ['required', 'string', 'size:6']]);

        if (! $user->two_factor_secret || ! $totp->verify((string) $user->two_factor_secret, $request->input('code'))) {
            return back()->withErrors(['code' => __('auth.two_factor.invalid_code')]);
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();
        session(['two_factor_verified_at' => now()->timestamp]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', __('app.account.two_factor_enabled'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user?->is_superadmin, 403);

        $request->validate(['password' => ['required', 'current_password:web']]);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
        session()->forget('two_factor_verified_at');

        return back()->with('success', __('app.account.two_factor_disabled'));
    }
}
