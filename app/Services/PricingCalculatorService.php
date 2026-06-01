<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

class PricingCalculatorService
{
    /**
     * @return array<string, float|int>
     */
    public function calculate(Product $product, float $profitMarginPercent): array
    {
        $product->loadMissing([
            'technicalSheets',
            'variableCosts',
            'additionalCosts',
            'laborCosts',
            'tenant.fixedCosts',
            'tenant.tenantVariableCosts',
            'tenant.products',
        ]);

        $materialsCost = (float) $product->technicalSheets->sum(
            fn ($sheet) => (float) $sheet->quantity * (float) $sheet->unit_cost
        );
        $variableCosts = (float) $product->variableCosts->sum('amount');
        $additionalCosts = (float) $product->additionalCosts->sum('amount');
        $laborCost = (float) $product->laborCosts->sum(
            fn ($labor) => (float) $labor->hourly_rate * (float) $labor->hours_spent
        );

        $totalFixedCosts = (float) $product->tenant->fixedCosts()
            ->where('is_active', true)
            ->sum('amount');

        $totalTenantVariableCosts = (float) $product->tenant->tenantVariableCosts()
            ->where('is_active', true)
            ->sum('amount');

        $activeProductsCount = $product->tenant->products()
            ->where('is_active', true)
            ->count();

        $fixedCostShare = $activeProductsCount > 0
            ? $totalFixedCosts / $activeProductsCount
            : 0.0;

        $tenantVariableCostShare = $activeProductsCount > 0
            ? $totalTenantVariableCosts / $activeProductsCount
            : 0.0;

        $totalProductionCost = $materialsCost
            + $variableCosts
            + $additionalCosts
            + $laborCost
            + $fixedCostShare
            + $tenantVariableCostShare;

        $finalPrice = $totalProductionCost * (1 + ($profitMarginPercent / 100));
        $profit = $finalPrice - $totalProductionCost;

        return [
            'materials_cost' => round($materialsCost, 2),
            'variable_costs' => round($variableCosts, 2),
            'additional_costs' => round($additionalCosts, 2),
            'labor_cost' => round($laborCost, 2),
            'fixed_cost_share' => round($fixedCostShare, 2),
            'tenant_variable_cost_share' => round($tenantVariableCostShare, 2),
            'total_production' => round($totalProductionCost, 2),
            'profit_margin_pct' => $profitMarginPercent,
            'profit_absolute' => round($profit, 2),
            'final_price' => round($finalPrice, 2),
        ];
    }
}
