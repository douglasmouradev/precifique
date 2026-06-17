<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendMonthlyGoalReminderJob;
use App\Mail\MonthlyGoalReminderMail;
use App\Models\MonthlyGoal;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class SendMonthlyGoalReminderJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_reminder_when_below_80_percent(): void
    {
        Mail::fake();

        $tenant = Tenant::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
        MonthlyGoal::create([
            'tenant_id' => $tenant->id,
            'year' => now()->year,
            'month' => now()->month,
            'goal_amount' => 10000,
        ]);

        (new SendMonthlyGoalReminderJob)->handle(app(TenantNotificationPreferences::class));

        Mail::assertQueued(MonthlyGoalReminderMail::class, fn ($mail) => $mail->hasTo($tenant->email));
    }
}
