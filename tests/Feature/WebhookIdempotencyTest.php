<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\WebhookEvent;
use App\Services\WebhookIdempotencyService;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class WebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_once_runs_handler_only_first_time(): void
    {
        $service = app(WebhookIdempotencyService::class);
        $runs = 0;

        $first = $service->processOnce('stripe', 'evt_test_123', function () use (&$runs) {
            $runs++;

            return true;
        });

        $second = $service->processOnce('stripe', 'evt_test_123', function () use (&$runs) {
            $runs++;

            return true;
        });

        $this->assertTrue($first);
        $this->assertTrue($second);
        $this->assertSame(1, $runs);
        $this->assertSame(1, WebhookEvent::where('provider', 'stripe')->where('event_id', 'evt_test_123')->count());
    }

    public function test_failed_handler_does_not_record_event(): void
    {
        $service = app(WebhookIdempotencyService::class);

        $first = $service->processOnce('stripe', 'evt_fail_1', fn () => false);
        $this->assertFalse($first);
        $this->assertSame(0, WebhookEvent::count());

        $runs = 0;
        $second = $service->processOnce('stripe', 'evt_fail_1', function () use (&$runs) {
            $runs++;

            return true;
        });

        $this->assertTrue($second);
        $this->assertSame(1, $runs);
        $this->assertSame(1, WebhookEvent::count());
    }
}
