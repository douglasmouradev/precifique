<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Models\Tenant;
use App\Support\SalePeriod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    public function generateMonthlyReport(Tenant $tenant, int $year, int $month): string
    {
        if (! extension_loaded('zip')) {
            throw new \RuntimeException(__('reports.excel.zip_required'));
        }

        $dir = storage_path('app/reports/'.$tenant->id);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new \RuntimeException(__('reports.excel.directory_failed'));
        }

        $this->purgeOldReports($dir);

        $spreadsheet = new Spreadsheet;
        $brandColor = '00C896';

        $this->buildSummarySheet($spreadsheet, $tenant, $year, $month, $brandColor);
        $this->buildProductsSheet($spreadsheet->createSheet(), $tenant, $brandColor);
        $this->buildSalesSheet($spreadsheet->createSheet(), $tenant, $year, $month, $brandColor);
        $this->buildCashFlowSheet($spreadsheet->createSheet(), $tenant, $year, $month, $brandColor);

        $spreadsheet->setActiveSheetIndex(0);

        $filename = __('reports.excel.filename_prefix')."_{$year}_{$month}.xlsx";
        $path = $dir.'/'.$filename;

        try {
            (new Xlsx($spreadsheet))->save($path);
        } finally {
            $spreadsheet->disconnectWorksheets();
        }

        if (! is_file($path)) {
            throw new \RuntimeException(__('reports.excel.save_failed'));
        }

        return $path;
    }

    /**
     * @return array{revenue: float, sales_count: int, fixed_costs: float, estimated_balance: float, has_data: bool}
     */
    public function monthlySummary(Tenant $tenant, int $year, int $month): array
    {
        $monthStats = SalePeriod::applyMonth($this->tenantSales($tenant), $year, $month)
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue, COUNT(*) as sales_count')
            ->first();

        $revenue = (float) ($monthStats->revenue ?? 0);
        $salesCount = (int) ($monthStats->sales_count ?? 0);
        $fixedCosts = (float) $this->tenantFixedCosts($tenant)->where('is_active', true)->sum('amount');

        return [
            'revenue' => $revenue,
            'sales_count' => $salesCount,
            'fixed_costs' => $fixedCosts,
            'estimated_balance' => $revenue - $fixedCosts,
            'has_data' => $salesCount > 0,
        ];
    }

    private function tenantSales(Tenant $tenant): HasMany
    {
        return $tenant->sales()->withoutGlobalScope('tenant');
    }

    private function tenantProducts(Tenant $tenant): HasMany
    {
        return $tenant->products()->withoutGlobalScope('tenant');
    }

    private function tenantFixedCosts(Tenant $tenant): HasMany
    {
        return $tenant->fixedCosts()->withoutGlobalScope('tenant');
    }

    private function paymentLabel(mixed $method): string
    {
        if ($method instanceof PaymentMethod) {
            return $method->label();
        }

        return PaymentMethod::tryLabel(is_string($method) ? $method : null);
    }

    private function buildSummarySheet(Spreadsheet $spreadsheet, Tenant $tenant, int $year, int $month, string $color): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(__('reports.excel.summary_sheet'));

        $sheet->setCellValue('A1', __('reports.excel.summary_title'));
        $sheet->setCellValue('A2', $tenant->name);
        $sheet->setCellValue('A3', sprintf('%02d/%d', $month, $year));

        $monthStats = SalePeriod::applyMonth($this->tenantSales($tenant), $year, $month)
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue, COUNT(*) as sales_count')
            ->first();

        $sheet->setCellValue('A5', __('reports.excel.revenue'));
        $sheet->setCellValue('B5', (float) ($monthStats->revenue ?? 0));
        $sheet->setCellValue('A6', __('reports.excel.sales_count'));
        $sheet->setCellValue('B6', (int) ($monthStats->sales_count ?? 0));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF'.$color);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    private function buildProductsSheet(Worksheet $sheet, Tenant $tenant, string $color): void
    {
        $sheet->setTitle(__('reports.excel.products_sheet'));
        $sheet->fromArray(__('reports.excel.products_headers'), null, 'A1');

        $row = 2;
        foreach ($this->tenantProducts($tenant)->orderBy('name')->get(['name', 'selling_price', 'profit_margin_percent', 'stock_quantity']) as $product) {
            $sheet->fromArray([
                $product->name,
                $product->selling_price,
                $product->profit_margin_percent,
                $product->stock_quantity,
            ], null, "A{$row}");
            $row++;
        }

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF'.$color);
    }

    private function buildSalesSheet(Worksheet $sheet, Tenant $tenant, int $year, int $month, string $color): void
    {
        $sheet->setTitle(__('reports.excel.sales_sheet'));
        $sheet->fromArray(__('reports.excel.sales_headers'), null, 'A1');

        $row = 2;
        $sales = SalePeriod::applyMonth($this->tenantSales($tenant), $year, $month)
            ->with(['product' => fn ($q) => $q->withoutGlobalScope('tenant')])
            ->orderByDesc('sold_at')
            ->get();

        foreach ($sales as $sale) {
            $sheet->fromArray([
                $sale->sold_at?->format('d/m/Y H:i') ?? '',
                $sale->product?->name,
                $sale->quantity,
                $sale->unit_price,
                $sale->total_amount,
                $this->paymentLabel($sale->payment_method),
            ], null, "A{$row}");
            $row++;
        }

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF'.$color);
    }

    private function buildCashFlowSheet(Worksheet $sheet, Tenant $tenant, int $year, int $month, string $color): void
    {
        $sheet->setTitle(__('reports.excel.cashflow_sheet'));
        $sheet->fromArray(__('reports.excel.cashflow_headers'), null, 'A1');

        $revenue = (float) SalePeriod::applyMonth($this->tenantSales($tenant), $year, $month)
            ->sum('total_amount');

        $fixedCosts = (float) $this->tenantFixedCosts($tenant)->where('is_active', true)->sum('amount');

        $sheet->fromArray([__('reports.excel.cashflow_income'), __('reports.excel.cashflow_sales'), $revenue], null, 'A2');
        $sheet->fromArray([__('reports.excel.cashflow_expense'), __('reports.excel.cashflow_fixed_costs'), $fixedCosts], null, 'A3');
        $sheet->fromArray([__('reports.excel.cashflow_balance'), __('reports.excel.cashflow_estimated'), $revenue - $fixedCosts], null, 'A4');

        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF'.$color);
    }

    private function purgeOldReports(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $maxAgeDays = (int) config('tenancy.report_retention_days', 90);
        $threshold = now()->subDays($maxAgeDays)->getTimestamp();

        foreach (glob($dir.'/*.xlsx') ?: [] as $file) {
            if (filemtime($file) < $threshold) {
                @unlink($file);
            }
        }
    }
}
