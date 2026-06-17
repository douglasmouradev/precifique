<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantWebhook;
use App\Models\WebhookDeliveryLog;
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

                $response = $request->post($hook->url, $body);
                $success = $response->successful();

                WebhookDeliveryLog::query()->create([
                    'tenant_webhook_id' => $hook->id,
                    'event' => $event,
                    'http_status' => $response->status(),
                    'success' => $success,
                    'error_message' => $success ? null : mb_substr((string) $response->body(), 0, 500),
                    'created_at' => now(),
                ]);

                if ($success) {
                    $hook->update(['last_triggered_at' => now()]);
                }
            } catch (\Throwable $e) {
                WebhookDeliveryLog::query()->create([
                    'tenant_webhook_id' => $hook->id,
                    'event' => $event,
                    'http_status' => null,
                    'success' => false,
                    'error_message' => mb_substr($e->getMessage(), 0, 500),
                    'created_at' => now(),
                ]);

                Log::warning('Tenant webhook failed', [
                    'webhook_id' => $hook->id,
                    'event' => $event,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
