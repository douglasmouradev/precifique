<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Models\Product;
use App\Models\Tenant;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class DuplicateProductAction
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function execute(Tenant $tenant, Product $product): Product
    {
        return DB::transaction(function () use ($tenant, $product) {
            $product->load(['technicalSheets', 'variableCosts', 'additionalCosts', 'laborCosts']);

            $copy = $tenant->products()->create([
                'name' => $product->name.' (cópia)',
                'description' => $product->description,
                'niche_type' => $product->niche_type,
                'niche_fields' => $product->niche_fields,
                'photo_path' => $product->photo_path,
                'production_time_minutes' => $product->production_time_minutes,
                'is_custom_order' => $product->is_custom_order,
                'profit_margin_percent' => $product->profit_margin_percent,
                'selling_price' => null,
            ]);

            foreach ($product->technicalSheets as $sheet) {
                $copy->technicalSheets()->create(array_merge(
                    ['tenant_id' => $tenant->id],
                    $sheet->only(['material_name', 'quantity', 'unit', 'unit_cost', 'supplier'])
                ));
            }

            foreach ($product->variableCosts as $cost) {
                $copy->variableCosts()->create([
                    'tenant_id' => $tenant->id,
                    'name' => $cost->name,
                    'amount' => $cost->amount,
                    'unit' => $cost->unit,
                ]);
            }

            foreach ($product->additionalCosts as $cost) {
                $copy->additionalCosts()->create([
                    'tenant_id' => $tenant->id,
                    'name' => $cost->name,
                    'amount' => $cost->amount,
                ]);
            }

            if ($labor = $product->laborCosts->first()) {
                $copy->laborCosts()->create([
                    'tenant_id' => $tenant->id,
                    'hourly_rate' => $labor->hourly_rate,
                    'hours_spent' => $labor->hours_spent,
                ]);
            }

            $this->audit->log($tenant, 'product.duplicated', $copy, ['from' => $product->id]);

            return $copy;
        });
    }
}
