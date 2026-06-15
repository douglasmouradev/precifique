<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LgpdConsent;
use App\Models\Tenant;
use App\Services\AdminMetricsService;
use Illuminate\Http\Request;
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

    public function tenants(Request $request): View
    {
        $query = Tenant::with('subscription.plan')->latest();

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($plan = $request->input('plan')) {
            $query->where('plan', $plan);
        }

        if ($status = $request->input('status')) {
            match ($status) {
                'active' => $query->where('is_active', true),
                'inactive' => $query->where('is_active', false),
                'trial' => $query->where('plan', '!=', 'premium')
                    ->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', now()),
                default => null,
            };
        }

        $tenants = $query->paginate(20)->withQueryString();
        $filters = $request->only(['q', 'plan', 'status']);

        return view('admin.tenants.index', compact('tenants', 'filters'));
    }

    public function lgpd(): View
    {
        $consents = LgpdConsent::with('tenant')->latest('consented_at')->paginate(30);

        return view('admin.lgpd', compact('consents'));
    }
}
