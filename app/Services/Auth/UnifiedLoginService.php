<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\Tenant;
use App\Models\TenantMember;
use App\Models\User;
use App\Services\SystemAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UnifiedLoginService
{
    public function __construct(
        private readonly SystemAuditService $systemAudit,
    ) {}

    /**
     * @param  array{email: string, password: string}  $credentials
     */
    public function attempt(Request $request, array $credentials): LoginAttemptResult
    {
        $remember = $request->boolean('remember');

        if (Auth::guard('tenant')->attempt($credentials, $remember)) {
            /** @var Tenant|null $tenant */
            $tenant = Auth::guard('tenant')->user();

            if ($tenant?->isDemoEmail() && ! Tenant::demoLoginEnabled()) {
                Auth::guard('tenant')->logout();

                return new LoginAttemptResult(false, back()
                    ->withErrors(['email' => __('auth.failed')])
                    ->onlyInput('email'));
            }

            return new LoginAttemptResult(true, $this->completeTenantLogin($request, $tenant, $remember));
        }

        $member = TenantMember::query()
            ->where('email', $credentials['email'])
            ->where('is_active', true)
            ->first();

        if ($member && Hash::check($credentials['password'], $member->password)) {
            return new LoginAttemptResult(true, $this->completeMemberLogin($request, $member, $remember));
        }

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            /** @var User|null $user */
            $user = Auth::guard('web')->user();
            if ($user?->is_superadmin) {
                $this->systemAudit->log('admin.login.success', $user, [
                    'email' => $credentials['email'],
                ], $request);
            }

            return new LoginAttemptResult(true, $this->completeWebLogin($request, $user, $remember));
        }

        if (User::query()->where('email', $credentials['email'])->where('is_superadmin', true)->exists()) {
            $this->systemAudit->log('admin.login.failed', null, [
                'email' => $credentials['email'],
            ], $request);
        }

        return new LoginAttemptResult(false, back()
            ->withErrors(['email' => __('auth.failed')])
            ->onlyInput('email'));
    }

    private function completeTenantLogin(Request $request, ?Tenant $tenant, bool $remember): RedirectResponse
    {
        if ($tenant?->hasTwoFactorEnabled() && ! $tenant->isDemoProfile()) {
            Auth::guard('tenant')->logout();
            $request->session()->put('tenant_login_two_factor_id', $tenant->id);
            $request->session()->put('tenant_login_remember', $remember);

            return redirect()->route('tenant.two-factor.challenge');
        }

        $request->session()->regenerate();
        session(['tenant_two_factor_verified_at' => now()->timestamp]);

        $tenant?->ensureTestEmailVerified();

        return redirect()->intended(route('tenant.dashboard'));
    }

    private function completeMemberLogin(Request $request, TenantMember $member, bool $remember): RedirectResponse
    {
        $tenant = $member->tenant;

        if ($tenant?->hasTwoFactorEnabled() && ! $tenant->isDemoProfile()) {
            $request->session()->put('tenant_login_two_factor_id', $tenant->id);
            $request->session()->put('tenant_login_member_id', $member->id);
            $request->session()->put('tenant_login_remember', $remember);

            return redirect()->route('tenant.two-factor.challenge');
        }

        Auth::guard('tenant_member')->login($member, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('tenant.dashboard'));
    }

    private function completeWebLogin(Request $request, ?User $user, bool $remember): RedirectResponse
    {
        if ($user?->is_superadmin && $user->hasTwoFactorEnabled()) {
            Auth::guard('web')->logout();
            $request->session()->put('login.two_factor_user_id', $user->id);
            $request->session()->put('login.remember', $remember);

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        if ($user?->is_superadmin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user?->hasTwoFactorEnabled()) {
            Auth::guard('web')->logout();
            $request->session()->put('login.two_factor_user_id', $user->id);
            $request->session()->put('login.remember', $remember);

            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
