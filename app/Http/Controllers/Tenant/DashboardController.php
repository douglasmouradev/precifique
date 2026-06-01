<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetricsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $metrics,
    ) {}

    public function index(): View
    {
        $tenant = Auth::guard('tenant')->user();

        return view('dashboard.index', $this->metrics->for($tenant));
    }
}
