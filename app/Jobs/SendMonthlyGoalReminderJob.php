<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\MonthlyGoalReminderMail;
use App\Models\MonthlyGoal;
use App\Models\Sale;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendMonthlyGoalReminderJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $now = now();

        Tenant::where('is_active', true)->each(function (Tenant $tenant) use ($now): void {
            $goal = MonthlyGoal::where('tenant_id', $tenant->id)
                ->where('year', $now->year)
                ->where('month', $now->month)
                ->first();

            if (! $goal) {
                return;
            }

            $revenue = (float) Sale::where('tenant_id', $tenant->id)
                ->whereYear('sold_at', $now->year)
                ->whereMonth('sold_at', $now->month)
                ->sum('total_amount');

            $progress = $goal->goal_amount > 0
                ? ($revenue / (float) $goal->goal_amount) * 100
                : 0;

            if ($progress < 80) {
                Mail::to($tenant->email)->send(new MonthlyGoalReminderMail($tenant, $goal, $revenue, $progress));
            }
        });
    }
}
