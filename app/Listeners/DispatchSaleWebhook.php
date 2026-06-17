<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SaleRecorded;
use App\Jobs\DispatchTenantWebhookJob;

class DispatchSaleWebhook
{
    public function handle(SaleRecorded $event): void
    {
        $sale = $event->sale;

        DispatchTenantWebhookJob::dispatch($event->tenant->id, 'sale.created', [
            'id' => $sale->id,
            'product_id' => $sale->product_id,
            'quantity' => $sale->quantity,
            'unit_price' => $sale->unit_price,
            'total_amount' => $sale->total_amount,
            'payment_method' => $sale->payment_method?->value ?? $sale->payment_method,
            'sold_at' => $sale->sold_at?->toIso8601String(),
        ]);
    }
}
