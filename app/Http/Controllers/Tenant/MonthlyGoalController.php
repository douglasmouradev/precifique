<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreMonthlyGoalRequest;
use App\Models\MonthlyGoal;
use App\Services\DashboardMetricsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MonthlyGoalController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $dashboardMetrics,
    ) {}

    public function edit(): View
    {
        $tenant = Auth::guard('tenant')->user();
        $now = now();

        $goal = MonthlyGoal::where('tenant_id', $tenant->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->first();

        return view('goals.edit', compact('goal'));
    }

    public function store(StoreMonthlyGoalRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();

        MonthlyGoal::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'year' => $request->integer('year'),
                'month' => $request->integer('month'),
            ],
            ['goal_amount' => $request->input('goal_amount')]
        );

        $this->dashboardMetrics->forget($tenant);

        return redirect()->route('tenant.dashboard')->with('success', 'Meta do mês definida.');
    }
}
