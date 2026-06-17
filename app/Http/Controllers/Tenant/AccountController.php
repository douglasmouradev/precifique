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
        $tenant = current_tenant();
        if (! $tenant) {
            $tenant = Auth::guard('tenant_member')->user()?->tenant;
        }
        abort_unless($tenant, 401);

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
            'members' => $tenant->members()->latest()->get(),
            'webhooks' => $tenant->webhooks()->with(['deliveryLogs' => fn ($q) => $q->latest('created_at')->limit(5)])->latest()->get(),
            'isOwner' => Auth::guard('tenant')->check(),
        ]);
    }

    public function updateProfile(UpdateAccountProfileRequest $request): RedirectResponse
    {
        abort_unless(Auth::guard('tenant')->check(), 403);

        current_tenant()?->update($request->profileAttributes());

        return back()->with('success', __('app.messages.profile_updated'));
    }

    public function updatePassword(UpdateAccountPasswordRequest $request): RedirectResponse
    {
        abort_unless(Auth::guard('tenant')->check(), 403);

        $tenant = current_tenant();
        $tenant?->update(['password' => $request->validated('password')]);

        return back()->with('success', __('app.messages.password_updated'));
    }
}
