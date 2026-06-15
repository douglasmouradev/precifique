<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\LowStockAlertJob;
use App\Mail\LowStockAlertMail;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use App\Services\TenantNotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class LowStockAlertJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_email_for_low_stock_products(): void
    {
        Mail::fake();
        Cache::forget('low_stock_alert_sent_'.now()->toDateString());

        $tenant = Tenant::factory()->create(['is_active' => true]);
        Product::factory()->create([
            'tenant_id' => $tenant->id,
            'stock_quantity' => 1,
            'min_stock_alert' => 5,
            'is_active' => true,
        ]);

        (new LowStockAlertJob)->handle(app(TenantNotificationService::class), app(TenantNotificationPreferences::class));

        Mail::assertQueued(LowStockAlertMail::class, function (LowStockAlertMail $mail) use ($tenant) {
            return $mail->tenant->is($tenant);
        });
    }

    public function test_job_skips_inactive_tenants(): void
    {
        Mail::fake();

        $tenant = Tenant::factory()->create(['is_active' => false]);
        Product::factory()->create([
            'tenant_id' => $tenant->id,
            'stock_quantity' => 0,
            'min_stock_alert' => 5,
            'is_active' => true,
        ]);

        (new LowStockAlertJob)->handle(app(TenantNotificationService::class), app(TenantNotificationPreferences::class));

        Mail::assertNothingSent();
    }
}
