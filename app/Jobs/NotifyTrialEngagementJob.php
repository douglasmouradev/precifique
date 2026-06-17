<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\TrialEngagementMail;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use App\Services\TenantNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class NotifyTrialEngagementJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function handle(TenantNotificationService $notifications, TenantNotificationPreferences $preferences): void
    {
        $daysList = config('precifique.trial.engagement_days', [3, 7]);

        foreach ($daysList as $daysAfterSignup) {
            $daysAfterSignup = (int) $daysAfterSignup;
            if ($daysAfterSignup < 1) {
                continue;
            }

            $signupDate = now()->subDays($daysAfterSignup)->toDateString();

            Tenant::query()
                ->where('plan', '!=', 'premium')
                ->where('is_active', true)
                ->whereNotNull('trial_ends_at')
                ->whereDate('created_at', $signupDate)
                ->chunkById(50, function ($tenants) use ($daysAfterSignup, $notifications, $preferences): void {
                    foreach ($tenants as $tenant) {
                        $cacheKey = "trial_engagement_{$tenant->id}_{$daysAfterSignup}";
                        if (Cache::has($cacheKey)) {
                            continue;
                        }

                        if ($preferences->allowsEmail($tenant, 'email_trial')) {
                            Mail::to($tenant->email)->send(new TrialEngagementMail($tenant, $daysAfterSignup));
                        }

                        if ($preferences->allowsInApp($tenant)) {
                            $notifications->notify(
                                $tenant,
                                'trial_engagement',
                                __('mail.trial_engagement.notification_title', ['day' => $daysAfterSignup]),
                                __('mail.trial_engagement.notification_body', ['day' => $daysAfterSignup]),
                                route('tenant.dashboard'),
                            );
                        }

                        Cache::put($cacheKey, true, now()->addDays(30));
                    }
                });
        }
    }
}
