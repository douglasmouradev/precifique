<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTenantVariableCostRequest;
use App\Http\Requests\Tenant\UpdateTenantVariableCostRequest;
use App\Models\TenantVariableCost;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantVariableCostController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(): View
    {
        $tenant = current_tenant();
        $variableCosts = $tenant->tenantVariableCosts()->latest()->get();
        $total = $variableCosts->where('is_active', true)->sum('amount');

        return view('variable-costs.index', compact('variableCosts', 'total'));
    }

    public function store(StoreTenantVariableCostRequest $request): RedirectResponse
    {
        $tenant = current_tenant();
        $cost = $tenant->tenantVariableCosts()->create($request->validated());
        $this->audit->log($tenant, 'tenant_variable_cost.created', $cost, [], $request);

        return back()->with('success', __('messages.variable_cost.created'));
    }

    public function update(UpdateTenantVariableCostRequest $request, TenantVariableCost $tenantVariableCost): RedirectResponse
    {
        $tenant = current_tenant();
        $this->authorize('update', $tenantVariableCost);

        $tenantVariableCost->update($request->validated());
        $this->audit->log($tenant, 'tenant_variable_cost.updated', $tenantVariableCost, [], $request);

        return back()->with('success', __('messages.variable_cost.updated'));
    }

    public function destroy(TenantVariableCost $tenantVariableCost): RedirectResponse
    {
        $tenant = current_tenant();
        $this->authorize('delete', $tenantVariableCost);

        $tenantVariableCost->delete();
        $this->audit->log($tenant, 'tenant_variable_cost.deleted', null, ['id' => $tenantVariableCost->id]);

        return back()->with('success', __('messages.variable_cost.removed'));
    }
}
