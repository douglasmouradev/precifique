<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\Concerns\AuthorizesTenantResource;
use App\Http\Controllers\Controller;
use App\Models\TenantWebhook;
use App\Rules\SafeWebhookUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    use AuthorizesTenantResource;

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeTenantManageAccount();
        $tenant = current_tenant();
        abort_unless($tenant, 403);

        $data = $request->validate([
            'url' => ['required', 'url', 'max:500', new SafeWebhookUrl],
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
        $this->authorizeTenantManageAccount();
        $tenant = current_tenant();
        abort_unless($tenant && $webhook->tenant_id === $tenant->id, 403);

        $webhook->delete();

        return back()->with('success', __('app.messages.token_revoked'));
    }
}
