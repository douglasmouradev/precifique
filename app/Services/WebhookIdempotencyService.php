<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;

class WebhookIdempotencyService
{
    /**
     * Processa o webhook apenas se o event_id ainda não foi registrado.
     *
     * @param  callable(): bool  $handler
     */
    public function processOnce(string $provider, string $eventId, callable $handler): bool
    {
        if ($eventId === '') {
            return false;
        }

        return DB::transaction(function () use ($provider, $eventId, $handler) {
            $existing = WebhookEvent::query()
                ->where('provider', $provider)
                ->where('event_id', $eventId)
                ->lockForUpdate()
                ->first();

            if ($existing !== null) {
                return true;
            }

            WebhookEvent::create([
                'provider' => $provider,
                'event_id' => $eventId,
                'processed_at' => now(),
            ]);

            return $handler();
        });
    }
}
