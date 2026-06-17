<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\MonthlyReportRequest;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reports,
    ) {}

    public function index(): View
    {
        $tenant = current_tenant();
        $year = now()->year;
        $month = now()->month;
        $summary = $tenant ? $this->reports->monthlySummary($tenant, $year, $month) : null;

        return view('reports.index', [
            'year' => $year,
            'month' => $month,
            'summary' => $summary,
        ]);
    }

    public function monthly(MonthlyReportRequest $request): BinaryFileResponse|RedirectResponse
    {
        $tenant = current_tenant();
        abort_if($tenant === null, 403);

        try {
            $path = $this->reports->generateMonthlyReport($tenant, $request->year(), $request->month());

            if (! is_file($path)) {
                throw new \RuntimeException('Report file was not created.');
            }

            return response()->download(
                $path,
                basename($path),
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            );
        } catch (Throwable $e) {
            report($e);

            $message = str_contains($e->getMessage(), 'Zip')
                ? __('reports.zip_missing')
                : __('reports.generate_failed');

            return redirect()
                ->route('tenant.reports.index')
                ->with('error', $message);
        }
    }
}
