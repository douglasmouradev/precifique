<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantPasswordResetController extends Controller
{
    public function showForgot(): View
    {
        return view('auth.tenant-forgot-password');
    }

    public function sendReset(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::broker('tenants')->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showReset(Request $request, string $token): View
    {
        return view('auth.tenant-reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::broker('tenants')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Tenant $tenant, string $password): void {
                $tenant->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($tenant));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('tenant.login')->with('success', 'Senha redefinida.')
            : back()->withErrors(['email' => __($status)]);
    }
}
