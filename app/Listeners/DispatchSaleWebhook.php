<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SaleRecorded;
use App\Services\TenantWebhookDispatcher;

class DispatchSaleWebhook
{
    public function __construct(
        private readonly TenantWebhookDispatcher $dispatcher,
    ) {}

    public function handle(SaleRecorded $event): void
    {
        $sale = $event->sale;

        $this->dispatcher->dispatch($event->tenant, 'sale.created', [
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
