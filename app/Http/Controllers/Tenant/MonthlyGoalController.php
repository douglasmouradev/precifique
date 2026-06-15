<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Events\TenantDashboardChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreMonthlyGoalRequest;
use App\Models\MonthlyGoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MonthlyGoalController extends Controller
{
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

        $goal = new MonthlyGoal([
            'tenant_id' => $tenant->id,
            'year' => $request->integer('year'),
            'month' => $request->integer('month'),
        ]);

        Gate::forUser($tenant)->authorize('update', $goal);

        $goal = MonthlyGoal::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'year' => $request->integer('year'),
                'month' => $request->integer('month'),
            ],
            ['goal_amount' => $request->input('goal_amount')]
        );

        TenantDashboardChanged::dispatch($tenant);

        return redirect()->route('tenant.dashboard')->with('success', 'Meta do mês definida.');
    }
}
