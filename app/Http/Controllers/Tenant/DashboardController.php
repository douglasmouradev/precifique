<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\DashboardMetricsService;
use App\Services\PlanLimitService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $metrics,
        private readonly PlanLimitService $planLimits,
    ) {}

    public function index(): View
    {
        $tenant = current_tenant();
        abort_unless($tenant instanceof Tenant, 403);

        $data = $this->metrics->for($tenant);
        $data['maxProducts'] = $this->planLimits->maxProducts($tenant);
        $data['productLimitCount'] = $this->planLimits->currentProductCount($tenant);

        return view('dashboard.index', $data);
    }
}
