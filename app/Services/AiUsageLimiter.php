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
    public function dailyLimit(): int
    {
        return (int) config('precifique.ai.premium_daily_limit', 50);
    }

    public function usedToday(Tenant $tenant): int
    {
        return (int) (TenantAiUsage::query()
            ->where('tenant_id', $tenant->id)
            ->whereDate('usage_date', Carbon::today())
            ->value('requests') ?? 0);
    }

    public function remainingToday(Tenant $tenant): int
    {
        return max(0, $this->dailyLimit() - $this->usedToday($tenant));
    }

    public function assertCanUse(Tenant $tenant): void
    {
        if (! $tenant->isPremium()) {
            throw new HttpResponseException(response()->json([
                'message' => __('messages.ai.premium_only'),
            ], 403));
        }

        $limit = $this->dailyLimit();
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
                    'message' => __('messages.ai.daily_limit', ['limit' => $limit]),
                ], 429));
            }

            $usage->increment('requests');
        });
    }
}
