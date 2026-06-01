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
        ];

        if (config('cache.default') === 'redis' || config('queue.default') === 'redis') {
            $checks['redis'] = $this->checkRedis();
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
        if (config('queue.default') === 'sync') {
            return true;
        }

        try {
            return config('queue.connections.'.config('queue.default')) !== null;
        } catch (\Throwable) {
            return false;
        }
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
}
