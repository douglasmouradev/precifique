<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ExportSalesCsvJob;
use App\Models\SaleExportRequest;
use App\Models\TenantNotification;
use App\Services\SalesExportService;
use App\Services\TenantNotificationService;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ExportSalesCsvJobTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_job_completes_export_and_notifies(): void
    {
        Storage::fake('local');
        $tenant = $this->readyTenant();

        $export = SaleExportRequest::create([
            'tenant_id' => $tenant->id,
            'status' => 'pending',
            'filters' => [],
        ]);

        (new ExportSalesCsvJob($export->id))->handle(
            app(SalesExportService::class),
            app(TenantNotificationService::class),
        );

        $export->refresh();
        $this->assertSame('completed', $export->status);
        $this->assertNotNull($export->file_path);
        $this->assertTrue(TenantNotification::where('tenant_id', $tenant->id)->where('type', 'export_ready')->exists());
    }
}
