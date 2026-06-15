<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ProductPriced;
use App\Events\SaleRecorded;
use App\Events\TenantDashboardChanged;
use App\Services\DashboardMetricsService;

class InvalidateDashboardCache
{
    public function __construct(
        private readonly DashboardMetricsService $dashboardMetrics,
    ) {}

    public function handle(SaleRecorded|ProductPriced|TenantDashboardChanged $event): void
    {
        $tenant = match (true) {
            $event instanceof TenantDashboardChanged => $event->tenant,
            $event instanceof SaleRecorded => $event->tenant,
            $event instanceof ProductPriced => $event->tenant,
        };

        $this->dashboardMetrics->forget($tenant);
    }
}
