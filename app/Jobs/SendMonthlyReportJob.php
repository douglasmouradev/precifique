<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMonthlyReportJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $date = now()->subMonth();
        $year = (int) $date->year;
        $month = (int) $date->month;

        Tenant::withPremiumAccess()
            ->select('id')
            ->chunkById(50, function ($tenants) use ($year, $month): void {
                foreach ($tenants as $tenant) {
                    SendTenantMonthlyReportJob::dispatch($tenant->id, $year, $month);
                }
            });
    }
}
