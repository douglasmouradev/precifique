<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\PixExpiringMail;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use App\Services\TenantNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class NotifyPixExpiringJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function handle(TenantNotificationService $notifications, TenantNotificationPreferences $preferences): void
    {
        $days = (int) config('precifique.pix.notify_days_before', 3);
        $target = now()->addDays($days)->startOfDay();

        Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('mercadopago_payment_id')
            ->whereNotNull('ends_at')
            ->whereDate('ends_at', $target)
            ->with('tenant')
            ->chunkById(50, function ($subscriptions) use ($notifications, $preferences): void {
                foreach ($subscriptions as $subscription) {
                    $tenant = $subscription->tenant;
                    if (! $tenant instanceof Tenant || ! $tenant->is_active) {
                        continue;
                    }

                    $cacheKey = "pix_expiring_notified_{$tenant->id}_{$subscription->ends_at?->toDateString()}";
                    if (Cache::has($cacheKey)) {
                        continue;
                    }

                    if ($preferences->allowsEmail($tenant, 'email_pix')) {
                        Mail::to($tenant->email)->queue(new PixExpiringMail($tenant, $subscription));
                    }

                    if ($preferences->allowsInApp($tenant)) {
                        $notifications->notify(
                            $tenant,
                            'pix_expiring',
                            __('billing.pix_renewal_notice_title'),
                            __('billing.pix_renewal_notice_body', ['date' => $subscription->ends_at?->format('d/m/Y')]),
                            route('tenant.billing.upgrade'),
                        );
                    }

                    Cache::put($cacheKey, true, now()->addDays(7));
                }
            });
    }
}
