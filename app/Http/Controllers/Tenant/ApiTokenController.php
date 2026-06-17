<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreApiTokenRequest;
use App\Http\Requests\Tenant\UpdateNotificationPreferencesRequest;
use App\Models\TenantApiToken;
use App\Support\TenantApiAbilities;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    public function store(StoreApiTokenRequest $request): RedirectResponse
    {
        $tenant = current_tenant();
        $abilities = $request->validated('abilities', TenantApiAbilities::defaultForWeb());

        $plain = TenantApiToken::issue($tenant, $request->validated('name'), $abilities);

        return back()
            ->with('api_token_plain', $plain)
            ->with('success', __('app.account.token_created'));
    }

    public function destroy(TenantApiToken $token): RedirectResponse
    {
        $tenant = current_tenant();
        abort_unless($token->tenant_id === $tenant->id, 403);

        $token->delete();

        return back()->with('success', __('app.messages.token_revoked'));
    }

    public function updatePreferences(UpdateNotificationPreferencesRequest $request): RedirectResponse
    {
        $tenant = current_tenant();
        $tenant->update(['notification_preferences' => $request->preferences()]);

        return back()->with('success', __('app.messages.preferences_updated'));
    }
}
