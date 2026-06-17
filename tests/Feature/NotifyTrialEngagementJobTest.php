<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\NotifyTrialEngagementJob;
use App\Mail\TrialEngagementMail;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use App\Services\TenantNotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class NotifyTrialEngagementJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_day_3_email_for_tenants_registered_three_days_ago(): void
    {
        Mail::fake();
        config(['precifique.trial.engagement_days' => [3, 7]]);

        $tenant = Tenant::factory()->create([
            'plan' => 'basic',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(11),
            'created_at' => now()->subDays(3),
        ]);

        (new NotifyTrialEngagementJob)->handle(
            app(TenantNotificationService::class),
            app(TenantNotificationPreferences::class),
        );

        Mail::assertQueued(TrialEngagementMail::class, function (TrialEngagementMail $mail) use ($tenant) {
            return $mail->tenant->is($tenant) && $mail->day === 3;
        });
    }

    public function test_job_does_not_send_duplicate_engagement_emails(): void
    {
        Mail::fake();
        config(['precifique.trial.engagement_days' => [3]]);

        $tenant = Tenant::factory()->create([
            'plan' => 'basic',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(11),
            'created_at' => now()->subDays(3),
        ]);

        Cache::put("trial_engagement_{$tenant->id}_3", true, now()->addDays(30));

        (new NotifyTrialEngagementJob)->handle(
            app(TenantNotificationService::class),
            app(TenantNotificationPreferences::class),
        );

        Mail::assertNothingSent();
    }
}
