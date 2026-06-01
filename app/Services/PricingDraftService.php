<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdditionalCost;
use App\Models\LaborCost;
use App\Models\Product;
use App\Models\TechnicalSheet;
use App\Models\VariableCost;
use Illuminate\Support\Collection;

class PricingDraftService
{
    public function __construct(
        private readonly PricingCalculatorService $calculator,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, float|int>
     */
    public function preview(Product $product, float $marginPercent, array $payload): array
    {
        $product->loadMissing(['tenant.fixedCosts', 'tenant.tenantVariableCosts', 'tenant.products']);

        $this->applyDraft($product, $payload);

        return $this->calculator->calculate($product, $marginPercent);
    }

    /** @param  array<string, mixed>  $payload */
    private function applyDraft(Product $product, array $payload): void
    {
        $product->setRelation('technicalSheets', $this->mapMaterials($payload['materials'] ?? []));
        $product->setRelation('variableCosts', $this->mapNamedCosts($payload['variable_costs'] ?? [], VariableCost::class));
        $product->setRelation('additionalCosts', $this->mapNamedCosts($payload['additional_costs'] ?? [], AdditionalCost::class));
        $product->setRelation('laborCosts', $this->mapLabor($payload));
    }

    /** @param  array<int, array<string, mixed>>  $rows */
    private function mapMaterials(array $rows): Collection
    {
        return collect($rows)
            ->filter(fn (array $row) => ! empty($row['material_name']))
            ->map(function (array $row) {
                $qty = (float) ($row['quantity'] ?? 0);
                $unitCost = (float) ($row['unit_cost'] ?? 0);

                return new TechnicalSheet([
                    'material_name' => $row['material_name'],
                    'quantity' => $qty,
                    'unit' => $row['unit'] ?? 'un',
                    'unit_cost' => $unitCost,
                    'total_cost' => round($qty * $unitCost, 2),
                ]);
            })
            ->values();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  class-string<VariableCost|AdditionalCost>  $model
     */
    private function mapNamedCosts(array $rows, string $model): Collection
    {
        return collect($rows)
            ->filter(fn (array $row) => ! empty($row['name']))
            ->map(fn (array $row) => new $model([
                'name' => $row['name'],
                'amount' => (float) ($row['amount'] ?? 0),
            ]))
            ->values();
    }

    /** @param  array<string, mixed>  $payload */
    private function mapLabor(array $payload): Collection
    {
        $hourly = (float) ($payload['hourly_rate'] ?? 0);
        $hours = (float) ($payload['hours_spent'] ?? 0);

        if ($hourly <= 0 && $hours <= 0) {
            return collect();
        }

        return collect([
            new LaborCost([
                'hourly_rate' => $hourly,
                'hours_spent' => $hours,
                'total_labor' => round($hourly * $hours, 2),
            ]),
        ]);
    }
}
