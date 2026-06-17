<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\DashboardMetricsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $metrics,
    ) {}

    public function index(): View
    {
        $tenant = current_tenant();
        abort_unless($tenant instanceof Tenant, 403);

        return view('dashboard.index', $this->metrics->for($tenant));
    }
}
