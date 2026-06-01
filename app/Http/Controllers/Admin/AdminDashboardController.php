<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LgpdConsent;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $mrr = (float) Subscription::active()
            ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->sum('plans.price_monthly');

        $premiumCount = Tenant::where('plan', 'premium')->count();

        $startOfMonth = now()->startOfMonth();
        $activeAtStart = Subscription::where('starts_at', '<', $startOfMonth)
            ->whereIn('status', ['active', 'cancelled'])
            ->where(function ($q) use ($startOfMonth) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $startOfMonth);
            })
            ->count();

        $cancelledThisMonth = Subscription::where('status', 'cancelled')
            ->where('ends_at', '>=', $startOfMonth)
            ->count();

        $churn = $activeAtStart > 0
            ? round(($cancelledThisMonth / $activeAtStart) * 100, 1)
            : 0;

        $newTenantsThisMonth = Tenant::where('created_at', '>=', $startOfMonth)->count();
        $onTrialCount = Tenant::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->where('plan', '!=', 'premium')
            ->count();

        $paidActive = Subscription::active()->count();
        $trialToPaidRate = ($onTrialCount + $paidActive) > 0
            ? round(($paidActive / ($onTrialCount + $paidActive)) * 100, 1)
            : 0;

        $arpu = $activeTenants > 0 ? round($mrr / max(1, $paidActive ?: 1), 2) : 0.0;

        $recentTenants = Tenant::latest()->limit(5)->get(['id', 'name', 'email', 'plan', 'created_at', 'is_active']);

        return view('admin.dashboard', compact(
            'totalTenants',
            'activeTenants',
            'mrr',
            'premiumCount',
            'churn',
            'newTenantsThisMonth',
            'onTrialCount',
            'trialToPaidRate',
            'arpu',
            'recentTenants',
        ));
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
