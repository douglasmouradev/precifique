<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

class AdminMetricsService
{
    public static function forgetCache(): void
    {
        Cache::forget('admin.dashboard.'.now()->format('Y-m'));
    }

    /**
     * @return array<string, mixed>
     */
    public function metrics(): array
    {
        return Cache::remember(
            'admin.dashboard.'.now()->format('Y-m'),
            now()->addMinutes(10),
            fn () => $this->build(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function build(): array
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

        return compact(
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
        );
    }
}
