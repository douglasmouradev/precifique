<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\LowStockAlertMail;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use App\Services\TenantNotificationService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class LowStockAlertJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $uniqueFor = 3600;

    public function uniqueId(): string
    {
        return 'low-stock-alert-'.now()->toDateString();
    }

    public function handle(TenantNotificationService $notifications, TenantNotificationPreferences $preferences): void
    {
        $cacheKey = 'low_stock_alert_sent_'.now()->toDateString();
        if (Cache::has($cacheKey)) {
            return;
        }

        $lowStockByTenant = Product::query()
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->whereIn('tenant_id', Tenant::query()->where('is_active', true)->select('id'))
            ->get()
            ->groupBy('tenant_id');

        if ($lowStockByTenant->isEmpty()) {
            Cache::put($cacheKey, true, now()->endOfDay());

            return;
        }

        $tenants = Tenant::query()
            ->whereIn('id', $lowStockByTenant->keys())
            ->get()
            ->keyBy('id');

        foreach ($lowStockByTenant as $tenantId => $products) {
            $tenant = $tenants->get($tenantId);
            if ($tenant) {
                if ($preferences->allowsEmail($tenant, 'email_low_stock')) {
                    Mail::to($tenant->email)->send(new LowStockAlertMail($tenant, $products));
                }
                if ($preferences->allowsInApp($tenant)) {
                    $notifications->notify(
                        $tenant,
                        'low_stock',
                        'Estoque baixo em '.$products->count().' produto(s)',
                        'Revise o estoque e reponha os itens em alerta.',
                        route('tenant.stock.index'),
                    );
                }
            }
        }

        Cache::put($cacheKey, true, now()->endOfDay());
    }
}
