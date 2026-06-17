<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantWebhook;
use App\Rules\SafeWebhookUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TenantWebhookDispatcher
{
    /** @param  array<string, mixed>  $payload */
    public function dispatch(Tenant $tenant, string $event, array $payload): void
    {
        $hooks = TenantWebhook::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get();

        foreach ($hooks as $hook) {
            if (! $hook->listensTo($event)) {
                continue;
            }

            if (! SafeWebhookUrl::isAllowed($hook->url)) {
                Log::warning('Tenant webhook blocked (unsafe URL)', [
                    'webhook_id' => $hook->id,
                    'event' => $event,
                ]);

                continue;
            }

            try {
                $body = [
                    'event' => $event,
                    'tenant_id' => $tenant->id,
                    'data' => $payload,
                    'sent_at' => now()->toIso8601String(),
                ];

                $request = Http::timeout(10)->asJson();
                if ($hook->secret) {
                    $request = $request->withHeaders([
                        'X-Precifique-Signature' => hash_hmac('sha256', json_encode($body), $hook->secret),
                    ]);
                }

                $request->post($hook->url, $body);
                $hook->update(['last_triggered_at' => now()]);
            } catch (\Throwable $e) {
                Log::warning('Tenant webhook failed', [
                    'webhook_id' => $hook->id,
                    'event' => $event,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
