<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\SaleExportRequest;
use App\Models\Tenant;
use App\Services\SalesExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExportSalesCsvJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly int $exportRequestId,
    ) {}

    public function handle(SalesExportService $exporter): void
    {
        $request = SaleExportRequest::query()->find($this->exportRequestId);
        if (! $request || $request->status !== 'pending') {
            return;
        }

        $tenant = Tenant::query()->find($request->tenant_id);
        if (! $tenant) {
            $request->update(['status' => 'failed']);

            return;
        }

        try {
            $path = $exporter->generateToStorage($tenant, $request->filters ?? []);
            $request->update([
                'status' => 'completed',
                'file_path' => $path,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Export sales CSV failed', [
                'export_id' => $request->id,
                'message' => $e->getMessage(),
            ]);
            $request->update(['status' => 'failed']);
        }
    }
}
