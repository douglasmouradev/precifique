<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'app' => true,
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
            'storage' => $this->checkStorage(),
        ];

        if (config('cache.default') === 'redis' || config('queue.default') === 'redis') {
            $checks['redis'] = $this->checkRedis();
        }

        if (config('queue.default') !== 'sync') {
            $checks['queue_worker'] = $this->checkQueueWorker();
        }

        $healthy = collect($checks)->every(fn ($v) => $v === true);

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function checkCache(): bool
    {
        try {
            Cache::put('health_check', '1', 5);

            return Cache::get('health_check') === '1';
        } catch (\Throwable) {
            return false;
        }
    }

    private function checkQueue(): bool
    {
        $driver = config('queue.default');

        if ($driver === 'sync') {
            return true;
        }

        if ($driver === 'redis') {
            return $this->checkRedis();
        }

        return config('queue.connections.'.$driver) !== null;
    }

    private function checkRedis(): bool
    {
        try {
            Redis::connection()->ping();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function checkStorage(): bool
    {
        try {
            return is_writable(storage_path('app')) && is_writable(storage_path('logs'));
        } catch (\Throwable) {
            return false;
        }
    }

    private function checkQueueWorker(): bool
    {
        try {
            $heartbeat = Cache::get('queue_worker_heartbeat');
            if (! is_string($heartbeat) || $heartbeat === '') {
                return false;
            }

            return now()->diffInMinutes(\Illuminate\Support\Carbon::parse($heartbeat)) <= 5;
        } catch (\Throwable) {
            return false;
        }
    }
}
