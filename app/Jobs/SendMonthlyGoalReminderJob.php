<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\MonthlyGoalReminderMail;
use App\Models\MonthlyGoal;
use App\Models\Tenant;
use App\Support\SalePeriod;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendMonthlyGoalReminderJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function handle(): void
    {
        $now = now();

        Tenant::query()
            ->where('is_active', true)
            ->chunkById(50, function ($tenants) use ($now): void {
                $tenantIds = $tenants->pluck('id');

                $goals = MonthlyGoal::query()
                    ->whereIn('tenant_id', $tenantIds)
                    ->where('year', $now->year)
                    ->where('month', $now->month)
                    ->get()
                    ->keyBy('tenant_id');

                foreach ($tenants as $tenant) {
                    $goal = $goals->get($tenant->id);
                    if (! $goal) {
                        continue;
                    }

                    $revenue = (float) SalePeriod::applyMonth(
                        $tenant->sales(),
                        $now->year,
                        $now->month
                    )->sum('total_amount');

                    $progress = $goal->goal_amount > 0
                        ? ($revenue / (float) $goal->goal_amount) * 100
                        : 0;

                    if ($progress < 80) {
                        Mail::to($tenant->email)->send(new MonthlyGoalReminderMail($tenant, $goal, $revenue, $progress));
                    }
                }
            });
    }
}
