<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\LowStockAlertMail;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class LowStockAlertJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Tenant::where('is_active', true)->each(function (Tenant $tenant): void {
            $lowStock = Product::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
                ->get();

            if ($lowStock->isNotEmpty()) {
                Mail::to($tenant->email)->send(new LowStockAlertMail($tenant, $lowStock));
            }
        });
    }
}
