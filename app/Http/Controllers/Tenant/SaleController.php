<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Events\SaleRecorded;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreSaleRequest;
use App\Http\Requests\Tenant\UpdateSaleRequest;
use App\Jobs\ExportSalesCsvJob;
use App\Models\Sale;
use App\Models\SaleExportRequest;
use App\Services\AuditService;
use App\Services\SalesExportService;
use App\Services\TenantNotificationService;
use App\Support\SalePeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly SalesExportService $salesExport,
        private readonly TenantNotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        $tenant = Auth::guard('tenant')->user();
        $filters = $request->only(['payment_method', 'month', 'year']);
        $query = $this->filteredSalesQuery($tenant, $filters);

        $stats = (clone $query)
            ->reorder()
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue, COUNT(*) as sales_count')
            ->first();

        $paymentBreakdown = (clone $query)
            ->reorder()
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $sales = (clone $query)
            ->with('product:id,name')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'totalRevenue' => (float) ($stats->total_revenue ?? 0),
            'salesCount' => (int) ($stats->sales_count ?? 0),
            'paymentBreakdown' => $paymentBreakdown,
            'filters' => $filters,
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $filters = $request->only(['payment_method', 'month', 'year']);
        $count = $this->filteredSalesQuery($tenant, $filters)->count();
        $threshold = (int) config('precifique.exports.sales_async_threshold', 200);

        if ($count > $threshold) {
            $exportRequest = SaleExportRequest::create([
                'tenant_id' => $tenant->id,
                'status' => 'pending',
                'filters' => $filters,
            ]);

            ExportSalesCsvJob::dispatch($exportRequest->id);

            if (config('queue.default') === 'sync') {
                $exportRequest->refresh();
                if ($exportRequest->status === 'completed' && $exportRequest->file_path) {
                    return redirect()->route('tenant.sales.export.download', $exportRequest)
                        ->with('success', __('messages.sale.export_done'));
                }
            }

            return redirect()->route('tenant.sales.index')
                ->with('success', __('messages.sale.export_processing'));
        }

        return $this->salesExport->streamDownload($tenant, $filters);
    }

    public function downloadExport(SaleExportRequest $saleExportRequest): StreamedResponse|RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        abort_unless($saleExportRequest->tenant_id === $tenant->id, 403);
        abort_unless($saleExportRequest->status === 'completed' && $saleExportRequest->file_path, 404);

        $disk = config('filesystems.default') === 's3' ? 's3' : 'local';

        if (! Storage::disk($disk)->exists($saleExportRequest->file_path)) {
            return redirect()->route('tenant.sales.index')->with('error', __('messages.sale.export_not_found'));
        }

        return Storage::disk($disk)->download(
            $saleExportRequest->file_path,
            'vendas-'.now()->format('Y-m-d').'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function create(): View
    {
        $tenant = Auth::guard('tenant')->user();
        $products = $tenant->products()->where('is_active', true)->orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $quantity = $request->integer('quantity');

        $sale = DB::transaction(function () use ($tenant, $request, $quantity) {
            $product = $tenant->products()->lockForUpdate()->findOrFail($request->integer('product_id'));

            if ($product->stock_quantity > 0 && $product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => __('messages.sale.insufficient_stock'),
                ]);
            }

            $sale = Sale::create([
                'tenant_id' => $tenant->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $request->input('unit_price'),
                'payment_method' => $request->input('payment_method'),
                'sold_at' => $request->input('sold_at') ?? now(),
                'notes' => $request->input('notes'),
            ]);

            if ($product->stock_quantity > 0) {
                $product->decrement('stock_quantity', $quantity);
            }

            return $sale;
        });

        $product = $tenant->products()->find($sale->product_id);

        $this->audit->log($tenant, 'sale.created', $sale, [
            'product' => $product?->name,
            'total' => $sale->total_amount,
        ], $request);

        SaleRecorded::dispatch($tenant, $sale);

        if ($tenant->sales()->count() === 1) {
            $this->notifications->notify(
                $tenant,
                'milestone',
                __('messages.sale.first_sale_title'),
                __('messages.sale.first_sale_body'),
                route('tenant.dashboard'),
            );
        }

        return redirect()->route('tenant.sales.index')->with('success', __('messages.sale.created'));
    }

    public function edit(Sale $sale): View
    {
        $this->authorize('update', $sale);
        $sale->load('product:id,name');

        return view('sales.edit', compact('sale'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $this->authorize('update', $sale);

        $newQuantity = $request->integer('quantity');

        DB::transaction(function () use ($tenant, $request, $sale, $newQuantity): void {
            if ($sale->product_id) {
                $product = $tenant->products()->lockForUpdate()->find($sale->product_id);
                if ($product && $product->stock_quantity > 0) {
                    $delta = $newQuantity - $sale->quantity;
                    if ($delta > 0 && $product->stock_quantity < $delta) {
                        throw ValidationException::withMessages([
                            'quantity' => __('messages.sale.insufficient_stock_edit'),
                        ]);
                    }
                    if ($delta !== 0) {
                        $product->decrement('stock_quantity', $delta);
                    }
                }
            }

            $sale->update([
                'quantity' => $newQuantity,
                'unit_price' => $request->input('unit_price'),
                'payment_method' => $request->input('payment_method'),
                'sold_at' => $request->input('sold_at'),
                'notes' => $request->input('notes'),
            ]);
        });

        $this->audit->log($tenant, 'sale.updated', $sale->fresh(), [
            'product' => $sale->product?->name,
            'total' => $sale->fresh()->total_amount,
            'quantity' => $newQuantity,
        ], $request);

        SaleRecorded::dispatch($tenant, $sale->fresh());

        return redirect()->route('tenant.sales.index')->with('success', __('messages.sale.updated'));
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $this->authorize('delete', $sale);

        DB::transaction(function () use ($sale, $tenant): void {
            if ($sale->product_id) {
                $product = $tenant->products()->lockForUpdate()->find($sale->product_id);
                if ($product && $product->stock_quantity >= 0) {
                    $product->increment('stock_quantity', $sale->quantity);
                }
            }

            SaleRecorded::dispatch($tenant, $sale);
            $sale->delete();
        });

        $this->audit->log($tenant, 'sale.deleted', null, ['sale_id' => $sale->id]);

        return back()->with('success', __('messages.sale.deleted'));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Sale>
     */
    private function filteredSalesQuery($tenant, array $filters)
    {
        $query = $tenant->sales()->latest('sold_at');

        if (! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        SalePeriod::applyFromFilters($query, $filters);

        return $query;
    }
}
