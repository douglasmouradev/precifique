<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LgpdConsent;
use App\Models\Tenant;
use App\Services\AdminMetricsService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly AdminMetricsService $metrics,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', $this->metrics->metrics());
    }

    public function tenants(): View
    {
        $tenants = Tenant::with('subscription.plan')->latest()->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function lgpd(): View
    {
        $consents = LgpdConsent::with('tenant')->latest('consented_at')->paginate(30);

        return view('admin.lgpd', compact('consents'));
    }
}
