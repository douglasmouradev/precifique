<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateAccountPasswordRequest;
use App\Http\Requests\Tenant\UpdateAccountProfileRequest;
use App\Models\Subscription;
use App\Models\TenantApiToken;
use App\Services\TenantNotificationPreferences;
use App\Support\TenantApiAbilities;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(TenantNotificationPreferences $preferences): View
    {
        $tenant = Auth::guard('tenant')->user();
        $subscription = Subscription::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->first();

        $tokens = TenantApiToken::query()
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->get(['id', 'name', 'abilities', 'last_used_at', 'expires_at', 'created_at']);

        return view('tenant.account', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'apiTokens' => $tokens,
            'apiAbilities' => TenantApiAbilities::all(),
            'notificationPrefs' => $preferences->for($tenant),
        ]);
    }

    public function updateProfile(UpdateAccountProfileRequest $request): RedirectResponse
    {
        Auth::guard('tenant')->user()->update($request->profileAttributes());

        return back()->with('success', __('app.messages.profile_updated'));
    }

    public function updatePassword(UpdateAccountPasswordRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $tenant->update(['password' => $request->validated('password')]);

        return back()->with('success', __('app.messages.password_updated'));
    }
}
