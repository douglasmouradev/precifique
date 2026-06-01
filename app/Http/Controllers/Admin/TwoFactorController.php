<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function show(TotpService $totp): View
    {
        $user = Auth::user();
        $secret = $user->two_factor_secret;

        if (! $secret) {
            $secret = $totp->generateSecret();
            $user->forceFill(['two_factor_secret' => $secret])->save();
        }

        $qrUri = $totp->getQrUri($secret, $user->email);

        return view('admin.two-factor', [
            'qrUri' => $qrUri,
            'secret' => $secret,
            'enabled' => $user->hasTwoFactorEnabled(),
        ]);
    }

    public function confirm(Request $request, TotpService $totp): RedirectResponse
    {
        $user = Auth::user();
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        if (! $user->two_factor_secret || ! $totp->verify((string) $user->two_factor_secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Código inválido. Confira o aplicativo autenticador.']);
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();
        session(['two_factor_verified_at' => now()->timestamp]);

        return back()->with('success', 'Autenticação em duas etapas ativada.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = Auth::user();
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return back()->with('success', '2FA desativado.');
    }
}
