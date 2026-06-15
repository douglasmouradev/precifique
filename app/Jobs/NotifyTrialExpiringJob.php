<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\TrialExpiringMail;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class NotifyTrialExpiringJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function handle(): void
    {
        $days = (int) config('precifique.trial.notify_days_before', 3);
        $target = now()->addDays($days)->startOfDay();

        Tenant::query()
            ->where('plan', '!=', 'premium')
            ->whereNotNull('trial_ends_at')
            ->whereDate('trial_ends_at', $target)
            ->where('is_active', true)
            ->chunkById(50, function ($tenants): void {
                foreach ($tenants as $tenant) {
                    $cacheKey = "trial_expiring_notified_{$tenant->id}_{$tenant->trial_ends_at?->toDateString()}";
                    if (Cache::has($cacheKey)) {
                        continue;
                    }

                    Mail::to($tenant->email)->send(new TrialExpiringMail($tenant));
                    Cache::put($cacheKey, true, now()->addDays(7));
                }
            });
    }
}
