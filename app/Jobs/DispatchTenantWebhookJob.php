<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use App\Services\TenantWebhookDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DispatchTenantWebhookJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly int $tenantId,
        public readonly string $event,
        public readonly array $payload,
    ) {}

    public function handle(TenantWebhookDispatcher $dispatcher): void
    {
        $tenant = Tenant::query()->find($this->tenantId);
        if (! $tenant) {
            return;
        }

        $dispatcher->dispatch($tenant, $this->event, $this->payload);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Tenant webhook job failed', [
            'tenant_id' => $this->tenantId,
            'event' => $this->event,
            'message' => $e->getMessage(),
        ]);
    }
}
