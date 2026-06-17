<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantWebhook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebhookController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->resolveTenant();
        abort_unless($tenant, 403);

        $data = $request->validate([
            'url' => ['required', 'url', 'max:500'],
            'secret' => ['nullable', 'string', 'max:255'],
        ]);

        $tenant->webhooks()->create([
            'url' => $data['url'],
            'secret' => $data['secret'] ?? null,
            'events' => ['sale.created'],
        ]);

        return back()->with('success', __('members.add_webhook'));
    }

    public function destroy(TenantWebhook $webhook): RedirectResponse
    {
        $tenant = $this->resolveTenant();
        abort_unless($tenant && $webhook->tenant_id === $tenant->id, 403);

        $webhook->delete();

        return back()->with('success', __('app.messages.token_revoked'));
    }

    private function resolveTenant()
    {
        return current_tenant()
            ?? Auth::guard('tenant_member')->user()?->tenant;
    }
}
