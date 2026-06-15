<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\MonthlyReportRequest;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reports,
    ) {}

    public function monthly(MonthlyReportRequest $request): BinaryFileResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $path = $this->reports->generateMonthlyReport($tenant, $request->year(), $request->month());

        return response()->download($path, basename($path));
    }
}
