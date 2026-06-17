<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\Concerns\AuthorizesTenantResource;
use App\Http\Controllers\Controller;
use App\Models\TenantWebhook;
use App\Rules\SafeWebhookUrl;
use App\Jobs\DispatchTenantWebhookJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'events' => ['nullable', 'array'],
            'events.*' => [Rule::in(['sale.created'])],
        ]);

        $tenant->webhooks()->create([
            'url' => $data['url'],
            'secret' => $data['secret'] ?? null,
            'events' => $data['events'] ?? ['sale.created'],
        ]);

        return back()->with('success', __('members.add_webhook'));
    }

    public function test(TenantWebhook $webhook): RedirectResponse
    {
        $this->authorizeTenantManageAccount();
        $tenant = current_tenant();
        abort_unless($tenant && $webhook->tenant_id === $tenant->id, 403);

        DispatchTenantWebhookJob::dispatch($tenant->id, 'webhook.test', [
            'webhook_id' => $webhook->id,
            'message' => 'ping',
        ]);

        return back()->with('success', __('members.webhook_test_sent'));
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
