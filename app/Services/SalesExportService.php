<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Models\Sale;
use App\Models\Tenant;
use App\Support\SalePeriod;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesExportService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function generateToStorage(Tenant $tenant, array $filters): string
    {
        $query = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->with('product')
            ->latest('sold_at');

        if (! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        SalePeriod::applyFromFilters($query, $filters);

        $filename = 'exports/tenant-'.$tenant->id.'/'.__('sales.export.filename_prefix').'-'.now()->format('Y-m-d-His').'.csv';
        $disk = config('filesystems.default') === 's3' ? 's3' : 'local';

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, $this->csvHeaders(), ';');

        $query->chunk(500, function ($sales) use ($handle): void {
            foreach ($sales as $sale) {
                fputcsv($handle, $this->csvRow($sale), ';');
            }
        });

        rewind($handle);
        Storage::disk($disk)->put($filename, stream_get_contents($handle) ?: '');
        fclose($handle);

        $this->purgeOldExports($tenant->id, $disk);

        return $filename;
    }

    public function purgeOldExports(int $tenantId, ?string $disk = null): void
    {
        $disk ??= config('filesystems.default') === 's3' ? 's3' : 'local';
        $dir = 'exports/tenant-'.$tenantId;
        $maxAgeDays = (int) config('precifique.exports.retention_days', 30);
        $threshold = now()->subDays($maxAgeDays)->getTimestamp();

        if (! Storage::disk($disk)->exists($dir)) {
            return;
        }

        foreach (Storage::disk($disk)->files($dir) as $file) {
            $lastModified = Storage::disk($disk)->lastModified($file);
            if ($lastModified < $threshold) {
                Storage::disk($disk)->delete($file);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function streamDownload(Tenant $tenant, array $filters): StreamedResponse
    {
        $query = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->with('product')
            ->latest('sold_at');

        if (! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        SalePeriod::applyFromFilters($query, $filters);

        $filename = __('sales.export.filename_prefix').'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $this->csvHeaders(), ';');
            foreach ($query->cursor() as $sale) {
                fputcsv($handle, $this->csvRow($sale), ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return list<string> */
    private function csvHeaders(): array
    {
        return array_values(__('sales.export.headers'));
    }

    /** @return list<int|float|string> */
    private function csvRow(Sale $sale): array
    {
        return [
            $sale->sold_at->format('d/m/Y H:i'),
            $sale->product?->name ?? __('sales.export.no_product'),
            $sale->quantity,
            number_format((float) $sale->unit_price, 2, ',', '.'),
            number_format((float) $sale->total_amount, 2, ',', '.'),
            PaymentMethod::tryLabel($sale->payment_method),
            $sale->notes ?? '',
        ];
    }
}
