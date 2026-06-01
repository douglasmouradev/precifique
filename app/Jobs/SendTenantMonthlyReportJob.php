<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\MonthlyReportMail;
use App\Models\Tenant;
use App\Services\ReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendTenantMonthlyReportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $tenantId,
        public int $year,
        public int $month,
    ) {}

    public function handle(ReportService $reports): void
    {
        $tenant = Tenant::where('is_active', true)
            ->where('plan', 'premium')
            ->find($this->tenantId);

        if (! $tenant) {
            return;
        }

        $path = $reports->generateMonthlyReport($tenant, $this->year, $this->month);
        Mail::to($tenant->email)->send(new MonthlyReportMail($tenant, $path));
    }
}
