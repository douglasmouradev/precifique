<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Models\Sale;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

class SalesExportService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function generateToStorage(Tenant $tenant, array $filters): string
    {
        $query = Sale::query()->where('tenant_id', $tenant->id)->with('product')->latest('sold_at');

        if (! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (! empty($filters['month'])) {
            $query->whereMonth('sold_at', (int) $filters['month']);
        }
        if (! empty($filters['year'])) {
            $query->whereYear('sold_at', (int) $filters['year']);
        } else {
            $query->whereYear('sold_at', now()->year);
        }

        $filename = 'exports/tenant-'.$tenant->id.'/vendas-'.now()->format('Y-m-d-His').'.csv';
        $disk = config('filesystems.default') === 's3' ? 's3' : 'local';

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, ['Data', 'Produto', 'Quantidade', 'Preço unitário', 'Total', 'Pagamento', 'Observações'], ';');

        $query->chunk(500, function ($sales) use ($handle): void {
            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->sold_at->format('d/m/Y H:i'),
                    $sale->product?->name ?? '—',
                    $sale->quantity,
                    number_format((float) $sale->unit_price, 2, ',', '.'),
                    number_format((float) $sale->total_amount, 2, ',', '.'),
                    PaymentMethod::tryLabel($sale->payment_method),
                    $sale->notes ?? '',
                ], ';');
            }
        });

        rewind($handle);
        Storage::disk($disk)->put($filename, stream_get_contents($handle) ?: '');
        fclose($handle);

        return $filename;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function streamDownload(Tenant $tenant, array $filters): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $sales = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->with('product')
            ->latest('sold_at');

        if (! empty($filters['payment_method'])) {
            $sales->where('payment_method', $filters['payment_method']);
        }
        if (! empty($filters['month'])) {
            $sales->whereMonth('sold_at', (int) $filters['month']);
        }
        if (! empty($filters['year'])) {
            $sales->whereYear('sold_at', (int) $filters['year']);
        } else {
            $sales->whereYear('sold_at', now()->year);
        }

        $filename = 'vendas-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($sales): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Data', 'Produto', 'Quantidade', 'Preço unitário', 'Total', 'Pagamento', 'Observações'], ';');
            foreach ($sales->cursor() as $sale) {
                fputcsv($handle, [
                    $sale->sold_at->format('d/m/Y H:i'),
                    $sale->product?->name ?? '—',
                    $sale->quantity,
                    number_format((float) $sale->unit_price, 2, ',', '.'),
                    number_format((float) $sale->total_amount, 2, ',', '.'),
                    PaymentMethod::tryLabel($sale->payment_method),
                    $sale->notes ?? '',
                ], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
