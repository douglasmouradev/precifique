<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $metrics,
    ) {}

    public function summary(): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $data = $this->metrics->for($tenant);

        return response()->json([
            'month_revenue' => $data['monthRevenue'],
            'sales_count' => $data['salesCount'],
            'goal_amount' => $data['goalAmount'],
            'goal_progress' => $data['goalProgress'],
            'products_count' => $data['productsCount'],
        ]);
    }
}
