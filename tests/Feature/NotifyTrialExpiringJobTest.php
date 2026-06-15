<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\NotifyTrialExpiringJob;
use App\Mail\TrialExpiringMail;
use App\Models\Tenant;
use App\Services\TenantNotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class NotifyTrialExpiringJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_email_for_tenants_with_trial_ending_soon(): void
    {
        Mail::fake();
        config(['precifique.trial.notify_days_before' => 3]);

        $tenant = Tenant::factory()->create([
            'plan' => 'basic',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(3),
        ]);

        (new NotifyTrialExpiringJob)->handle(app(TenantNotificationService::class));

        Mail::assertQueued(TrialExpiringMail::class, function (TrialExpiringMail $mail) use ($tenant) {
            return $mail->tenant->is($tenant);
        });
    }

    public function test_job_does_not_send_duplicate_emails(): void
    {
        Mail::fake();
        config(['precifique.trial.notify_days_before' => 3]);

        $tenant = Tenant::factory()->create([
            'plan' => 'basic',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(3),
        ]);

        $cacheKey = "trial_expiring_notified_{$tenant->id}_{$tenant->trial_ends_at->toDateString()}";
        Cache::put($cacheKey, true, now()->addDays(7));

        (new NotifyTrialExpiringJob)->handle(app(TenantNotificationService::class));

        Mail::assertNothingSent();
    }

    public function test_job_skips_premium_tenants(): void
    {
        Mail::fake();
        config(['precifique.trial.notify_days_before' => 3]);

        Tenant::factory()->create([
            'plan' => 'premium',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(3),
        ]);

        (new NotifyTrialExpiringJob)->handle(app(TenantNotificationService::class));

        Mail::assertNothingSent();
    }
}
