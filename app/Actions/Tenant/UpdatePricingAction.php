<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Events\ProductPriced;
use App\Http\Requests\Tenant\UpdatePricingRequest;
use App\Models\AdditionalCost;
use App\Models\LaborCost;
use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\TechnicalSheet;
use App\Models\Tenant;
use App\Models\VariableCost;
use App\Services\AuditService;
use App\Services\PricingCalculatorService;
use Illuminate\Support\Facades\DB;

class UpdatePricingAction
{
    public function __construct(
        private readonly PricingCalculatorService $calculator,
        private readonly AuditService $audit,
    ) {}

    /**
     * @return array{product: Product, result: array<string, mixed>, old_price: float|string|null}
     */
    public function execute(UpdatePricingRequest $request, Product $product, Tenant $tenant): array
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $product, $tenant, $data) {
            $nicheFields = $this->buildNicheFields($request, $tenant);

            $product->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_custom_order' => $request->boolean('is_custom_order'),
                'production_time_minutes' => $data['production_time_minutes'] ?? null,
                'stock_quantity' => $data['stock_quantity'] ?? 0,
                'min_stock_alert' => $data['min_stock_alert'] ?? 5,
                'profit_margin_percent' => $data['profit_margin_percent'],
                'niche_fields' => $nicheFields,
            ]);

            $this->syncMaterials($product, $data['materials'] ?? []);
            $this->syncVariableCosts($product, $tenant->id, $data['variable_costs'] ?? []);
            $this->syncAdditionalCosts($product, $tenant->id, $data['additional_costs'] ?? []);

            if (isset($data['hourly_rate'], $data['hours_spent'])) {
                LaborCost::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'tenant_id' => $tenant->id,
                        'hourly_rate' => $data['hourly_rate'],
                        'hours_spent' => $data['hours_spent'],
                    ]
                );
            }

            $product->refresh()->load([
                'technicalSheets', 'variableCosts', 'additionalCosts', 'laborCosts',
                'tenant.fixedCosts', 'tenant.tenantVariableCosts', 'tenant.products',
            ]);

            $result = $this->calculator->calculate($product, (float) $data['profit_margin_percent']);
            $oldPrice = $product->selling_price;
            $product->update(['selling_price' => $result['final_price']]);

            if ($oldPrice === null || (float) $oldPrice !== (float) $result['final_price']) {
                ProductPriceHistory::create([
                    'tenant_id' => $tenant->id,
                    'product_id' => $product->id,
                    'selling_price' => $result['final_price'],
                    'profit_margin_percent' => $data['profit_margin_percent'],
                    'production_cost' => $result['total_production'] ?? null,
                    'source' => 'pricing',
                ]);
            }

            $this->audit->log($tenant, 'product.price_updated', $product, [
                'old_price' => $oldPrice,
                'new_price' => $result['final_price'],
                'margin' => $data['profit_margin_percent'],
            ], $request);

            ProductPriced::dispatch($tenant, $product);

            return [
                'product' => $product->fresh(),
                'result' => $result,
                'old_price' => $oldPrice,
            ];
        });
    }

    /** @return array<string, mixed> */
    private function buildNicheFields(UpdatePricingRequest $request, Tenant $tenant): array
    {
        return match ($tenant->interface_mode) {
            'alimentos' => [
                'portion_yield' => $request->input('niche_fields.portion_yield'),
                'shelf_life' => $request->input('niche_fields.shelf_life'),
                'storage_temp' => $request->input('niche_fields.storage_temp'),
                'nutrition_label' => $request->input('niche_fields.nutrition_label'),
            ],
            'servico' => [
                'minimum_visit_fee' => $request->input('niche_fields.minimum_visit_fee'),
                'travel_cost' => $request->input('niche_fields.travel_cost'),
                'tools_cost' => $request->input('niche_fields.tools_cost'),
            ],
            'artesanato' => [
                'collection' => $request->input('niche_fields.collection'),
                'production_line' => $request->input('niche_fields.production_line'),
            ],
            default => $request->input('niche_fields', []) ?? [],
        };
    }

    /** @param  array<int, array<string, mixed>>  $materials */
    private function syncMaterials(Product $product, array $materials): void
    {
        $product->technicalSheets()->delete();
        foreach ($materials as $row) {
            if (empty($row['material_name'])) {
                continue;
            }
            TechnicalSheet::create([
                'tenant_id' => $product->tenant_id,
                'product_id' => $product->id,
                'material_name' => $row['material_name'],
                'quantity' => $row['quantity'] ?? 0,
                'unit' => $row['unit'] ?? 'un',
                'unit_cost' => $row['unit_cost'] ?? 0,
                'supplier' => $row['supplier'] ?? null,
            ]);
        }
    }

    /** @param  array<int, array<string, mixed>>  $costs */
    private function syncVariableCosts(Product $product, int $tenantId, array $costs): void
    {
        $product->variableCosts()->delete();
        foreach ($costs as $row) {
            if (empty($row['name'])) {
                continue;
            }
            VariableCost::create([
                'product_id' => $product->id,
                'tenant_id' => $tenantId,
                'name' => $row['name'],
                'amount' => $row['amount'] ?? 0,
                'unit' => $row['unit'] ?? null,
            ]);
        }
    }

    /** @param  array<int, array<string, mixed>>  $costs */
    private function syncAdditionalCosts(Product $product, int $tenantId, array $costs): void
    {
        $product->additionalCosts()->delete();
        foreach ($costs as $row) {
            if (empty($row['name'])) {
                continue;
            }
            AdditionalCost::create([
                'product_id' => $product->id,
                'tenant_id' => $tenantId,
                'name' => $row['name'],
                'amount' => $row['amount'] ?? 0,
            ]);
        }
    }
}
