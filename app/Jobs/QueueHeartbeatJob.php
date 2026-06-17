<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class QueueHeartbeatJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Cache::put('queue_worker_heartbeat', now()->toIso8601String(), now()->addMinutes(10));
    }
}
