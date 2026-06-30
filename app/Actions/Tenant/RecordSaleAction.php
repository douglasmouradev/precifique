<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Models\Sale;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordSaleAction
{
    /**
     * Registra uma venda com lock de estoque e débito transacional.
     *
     * @param  array{
     *     product_id: int,
     *     quantity: int,
     *     unit_price: mixed,
     *     payment_method: mixed,
     *     sold_at?: mixed,
     *     notes?: string|null,
     * }  $data
     */
    public function execute(Tenant $tenant, array $data): Sale
    {
        $quantity = (int) $data['quantity'];

        return DB::transaction(function () use ($tenant, $data, $quantity) {
            $product = $tenant->products()->lockForUpdate()->findOrFail((int) $data['product_id']);

            if ($product->stock_quantity > 0 && $product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => __('messages.sale.insufficient_stock'),
                ]);
            }

            $sale = Sale::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $data['unit_price'],
                'payment_method' => $data['payment_method'],
                'sold_at' => $data['sold_at'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);

            if ($product->stock_quantity > 0) {
                $product->decrement('stock_quantity', $quantity);
            }

            return $sale;
        });
    }
}
