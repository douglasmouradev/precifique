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

        $mrrTrend = $this->mrrTrend();
        $funnel = $this->onboardingFunnel();
        $signupTrend = $this->signupTrend();

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
            'mrrTrend',
            'funnel',
            'signupTrend',
        );
    }

    /** @return list<array{month: string, count: int}> */
    private function signupTrend(): array
    {
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trend[] = [
                'month' => $month->format('m/y'),
                'count' => Tenant::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        return $trend;
    }

    /** @return list<array{month: string, mrr: float}> */
    private function mrrTrend(): array
    {
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $mrr = (float) Subscription::query()
                ->where('status', 'active')
                ->where('starts_at', '<=', $month->copy()->endOfMonth())
                ->where(function ($q) use ($month) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', $month->copy()->startOfMonth());
                })
                ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                ->sum('plans.price_monthly');

            $trend[] = [
                'month' => $month->format('m/y'),
                'mrr' => $mrr,
            ];
        }

        return $trend;
    }

    /** @return array<string, int> */
    private function onboardingFunnel(): array
    {
        $registered = Tenant::count();
        $lgpd = Tenant::whereHas('lgpdConsents', fn ($q) => $q->where('consent_type', 'terms'))->count();
        $onboarded = Tenant::where('onboarding_completed', true)->count();
        $withProduct = Tenant::whereHas('products')->count();
        $withSale = Tenant::whereHas('sales')->count();

        return compact('registered', 'lgpd', 'onboarded', 'withProduct', 'withSale');
    }
}
