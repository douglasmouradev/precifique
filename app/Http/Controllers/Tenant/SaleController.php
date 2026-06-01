<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreSaleRequest;
use App\Events\SaleRecorded;
use App\Jobs\ExportSalesCsvJob;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleExportRequest;
use App\Services\AuditService;
use App\Services\SalesExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly SalesExportService $salesExport,
    ) {}

    public function index(Request $request): View
    {
        $tenant = Auth::guard('tenant')->user();
        $query = $this->filteredSalesQuery($tenant, $request);

        $sales = (clone $query)->paginate(15)->withQueryString();

        $summaryQuery = $this->filteredSalesQuery($tenant, $request);
        $totalRevenue = (float) (clone $summaryQuery)->sum('total_amount');
        $salesCount = (clone $summaryQuery)->count();

        $paymentBreakdown = (clone $summaryQuery)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        return view('sales.index', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'salesCount' => $salesCount,
            'paymentBreakdown' => $paymentBreakdown,
            'filters' => $request->only(['payment_method', 'month', 'year']),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $filters = $request->only(['payment_method', 'month', 'year']);
        $count = $this->filteredSalesQuery($tenant, $request)->count();
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
                        ->with('success', 'Exportação concluída.');
                }
            }

            return redirect()->route('tenant.sales.index')
                ->with('success', 'Exportação em processamento. Atualize a página em instantes para baixar.');
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
            return redirect()->route('tenant.sales.index')->with('error', 'Arquivo de exportação não encontrado.');
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
        $product = $tenant->products()->findOrFail($request->integer('product_id'));

        $sale = Sale::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'quantity' => $request->integer('quantity'),
            'unit_price' => $request->input('unit_price'),
            'payment_method' => $request->input('payment_method'),
            'sold_at' => $request->input('sold_at') ?? now(),
            'notes' => $request->input('notes'),
        ]);

        if ($product->stock_quantity > 0) {
            $product->decrement('stock_quantity', min($product->stock_quantity, $request->integer('quantity')));
        }

        $this->audit->log($tenant, 'sale.created', $sale, [
            'product' => $product->name,
            'total' => $sale->total_amount,
        ], $request);

        SaleRecorded::dispatch($tenant, $sale);

        return redirect()->route('tenant.sales.index')->with('success', 'Venda registrada.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $this->authorize('delete', $sale);

        SaleRecorded::dispatch($tenant, $sale);
        $sale->delete();
        $this->audit->log($tenant, 'sale.deleted', null, ['sale_id' => $sale->id]);

        return back()->with('success', 'Venda removida.');
    }

    /** @return \Illuminate\Database\Eloquent\Builder<Sale> */
    private function filteredSalesQuery($tenant, Request $request)
    {
        $query = $tenant->sales()->latest('sold_at');

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        if ($request->filled('month')) {
            $query->whereMonth('sold_at', (int) $request->input('month'));
        }

        if ($request->filled('year')) {
            $query->whereYear('sold_at', (int) $request->input('year'));
        } else {
            $query->whereYear('sold_at', now()->year);
        }

        return $query;
    }
}
