<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reports,
    ) {}

    public function monthly(Request $request): BinaryFileResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $path = $this->reports->generateMonthlyReport($tenant, $year, $month);

        return response()->download($path, basename($path));
    }
}
