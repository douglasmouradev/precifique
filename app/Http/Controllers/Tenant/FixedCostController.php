<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreFixedCostRequest;
use App\Http\Requests\Tenant\UpdateFixedCostRequest;
use App\Models\FixedCost;
use App\Services\AuditService;
use App\Services\DashboardMetricsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FixedCostController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly DashboardMetricsService $dashboardMetrics,
    ) {}

    public function index(): View
    {
        $tenant = Auth::guard('tenant')->user();
        $fixedCosts = $tenant->fixedCosts()->latest()->get();
        $total = $fixedCosts->where('is_active', true)->sum('amount');

        return view('fixed-costs.index', compact('fixedCosts', 'total'));
    }

    public function store(StoreFixedCostRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $cost = $tenant->fixedCosts()->create($request->validated());
        $this->audit->log($tenant, 'fixed_cost.created', $cost, [], $request);
        $this->dashboardMetrics->forget($tenant);

        return back()->with('success', 'Custo fixo adicionado.');
    }

    public function update(UpdateFixedCostRequest $request, FixedCost $fixedCost): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $this->authorize('update', $fixedCost);

        $fixedCost->update($request->validated());
        $this->audit->log($tenant, 'fixed_cost.updated', $fixedCost, [], $request);

        return back()->with('success', 'Custo fixo atualizado.');
    }

    public function destroy(FixedCost $fixedCost): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $this->authorize('delete', $fixedCost);

        $fixedCost->delete();
        $this->audit->log($tenant, 'fixed_cost.deleted', null, ['id' => $fixedCost->id]);

        return back()->with('success', 'Custo fixo removido.');
    }
}
