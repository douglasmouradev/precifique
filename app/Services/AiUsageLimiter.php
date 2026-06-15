<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantAiUsage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AiUsageLimiter
{
    public function assertCanUse(Tenant $tenant): void
    {
        if (! $tenant->isPremium()) {
            throw new HttpResponseException(response()->json([
                'message' => 'IA disponível no plano Premium.',
            ], 403));
        }

        $limit = (int) config('precifique.ai.premium_daily_limit', 50);
        $today = Carbon::today();

        DB::transaction(function () use ($tenant, $limit, $today): void {
            $usage = TenantAiUsage::query()
                ->where('tenant_id', $tenant->id)
                ->whereDate('usage_date', $today)
                ->lockForUpdate()
                ->first();

            if (! $usage) {
                $usage = TenantAiUsage::create([
                    'tenant_id' => $tenant->id,
                    'usage_date' => $today->toDateString(),
                    'requests' => 0,
                ]);
            }

            if ($usage->requests >= $limit) {
                throw new HttpResponseException(response()->json([
                    'message' => "Limite diário de IA atingido ({$limit} consultas). Tente amanhã.",
                ], 429));
            }

            $usage->increment('requests');
        });
    }
}
