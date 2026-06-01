<?php

declare(strict_types=1);

use App\Jobs\ExpireSubscriptionsJob;
use App\Jobs\NotifyTrialExpiringJob;
use App\Jobs\LowStockAlertJob;
use App\Jobs\SendMonthlyGoalReminderJob;
use App\Jobs\SendMonthlyReportJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendMonthlyReportJob)->monthlyOn(1, '08:00');
Schedule::job(new SendMonthlyGoalReminderJob)->weeklyOn(1, '09:00');
Schedule::job(new LowStockAlertJob)->dailyAt('07:00');
Schedule::job(new ExpireSubscriptionsJob)->dailyAt('02:00');
Schedule::job(new NotifyTrialExpiringJob)->dailyAt('09:00');
