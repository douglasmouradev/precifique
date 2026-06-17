<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Models\MonthlyGoal;
use App\Models\Sale;
use App\Models\Tenant;
use App\Support\SalePeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    public function __construct(
        private readonly AIAssistantService $ai,
        private readonly TenantSetupProgressService $setupProgress,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function for(Tenant $tenant): array
    {
        $cacheKey = $this->cacheKey($tenant);

        return Cache::remember($cacheKey, now()->addMinutes(5), fn () => $this->build($tenant));
    }

    public function forget(Tenant $tenant): void
    {
        Cache::forget($this->cacheKey($tenant));
    }

    private function cacheKey(Tenant $tenant): string
    {
        return 'tenant.'.$tenant->id.'.dashboard.'.now()->format('Y-m');
    }

    /**
     * @return array<string, mixed>
     */
    private function build(Tenant $tenant): array
    {
        $now = now();
        [$monthStart, $monthEnd] = SalePeriod::bounds($now->year, $now->month);

        $monthStats = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue, COUNT(*) as sales_count')
            ->first();

        $monthRevenue = (float) ($monthStats->revenue ?? 0);
        $salesCount = (int) ($monthStats->sales_count ?? 0);

        $goal = MonthlyGoal::query()
            ->where('tenant_id', $tenant->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->first();

        $goalAmount = (float) ($goal?->goal_amount ?? 0);
        $goalProgress = $goalAmount > 0 ? min(100, ($monthRevenue / $goalAmount) * 100) : 0;

        $productStats = $tenant->products()
            ->where('is_active', true)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN selling_price IS NULL OR selling_price <= 0 THEN 1 ELSE 0 END) as without_price')
            ->first();

        $productsCount = (int) ($productStats->total ?? 0);
        $productsWithoutPrice = (int) ($productStats->without_price ?? 0);
        $fixedCostsCount = $tenant->fixedCosts()->where('is_active', true)->count();

        $onboardingSteps = $this->setupProgress->forDashboard($tenant);
        $onboardingComplete = collect($onboardingSteps)->every(fn ($s) => $s['done']);

        $yearExpr = sql_year('sold_at');
        $monthExpr = sql_month('sold_at');

        $revenueChart = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->select(
                DB::raw("{$yearExpr} as year"),
                DB::raw("{$monthExpr} as month"),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('sold_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy(DB::raw($yearExpr), DB::raw($monthExpr))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $paymentCountsRaw = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method');

        $paymentLabels = [];
        $paymentCounts = [];
        $paymentColors = [];
        foreach (PaymentMethod::cases() as $method) {
            $paymentLabels[] = $method->label();
            $paymentCounts[] = (int) ($paymentCountsRaw[$method->value] ?? 0);
            $paymentColors[] = $method->chartColor();
        }

        $topProducts = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->select('product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->get()
            ->load(['product:id,name']);

        $recentSales = Sale::query()
            ->where('tenant_id', $tenant->id)
            ->with('product:id,name')
            ->latest('sold_at')
            ->limit(10)
            ->get();

        $aiTip = $tenant->isPremium()
            ? $this->cachedDailyTip($tenant)
            : null;

        return [
            'tenant' => $tenant,
            'monthRevenue' => $monthRevenue,
            'goalAmount' => $goalAmount,
            'goalProgress' => $goalProgress,
            'salesCount' => $salesCount,
            'productsCount' => $productsCount,
            'productsWithoutPrice' => $productsWithoutPrice,
            'fixedCostsCount' => $fixedCostsCount,
            'revenueChart' => $revenueChart,
            'paymentSalesTotal' => array_sum($paymentCounts),
            'paymentColors' => $paymentColors,
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'aiTip' => $aiTip,
            'revenueChartLabels' => $revenueChart->map(
                fn ($row) => str_pad((string) $row->month, 2, '0', STR_PAD_LEFT).'/'.$row->year
            )->values(),
            'revenueChartTotals' => $revenueChart->pluck('total')->values(),
            'paymentLabels' => $paymentLabels,
            'paymentCounts' => $paymentCounts,
            'topProductLabels' => $topProducts->map(fn ($row) => $row->product?->name ?? '—')->values(),
            'topProductQty' => $topProducts->pluck('qty')->values(),
            'onboardingSteps' => $onboardingSteps,
            'onboardingComplete' => $onboardingComplete,
        ];
    }

    private function cachedDailyTip(Tenant $tenant): string
    {
        try {
            $niche = $tenant->niche?->value ?? 'alimentos';
            $key = 'tenant.'.$tenant->id.'.ai_tip.'.now()->toDateString();

            return Cache::remember($key, now()->endOfDay(), fn () => $this->ai->dailyTip($niche));
        } catch (\Throwable) {
            return __('messages.ai.fallback_tip');
        }
    }
}
