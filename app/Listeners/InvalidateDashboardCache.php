<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ProductPriced;
use App\Events\SaleRecorded;
use App\Services\DashboardMetricsService;

class InvalidateDashboardCache
{
    public function __construct(
        private readonly DashboardMetricsService $dashboardMetrics,
    ) {}

    public function handle(SaleRecorded|ProductPriced $event): void
    {
        $this->dashboardMetrics->forget($event->tenant);
    }
}
