<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TenantSetupProgressService
{
    private const int CACHE_TTL_SECONDS = 300;

    /**
     * @return array{percent: int, completed: int, total: int, steps: list<array{key: string, label: string, done: bool, url: string|null}>}
     */
    public function for(Tenant $tenant): array
    {
        return Cache::remember(
            $this->cacheKey($tenant->id, 'full'),
            self::CACHE_TTL_SECONDS,
            fn () => $this->computeFor($tenant),
        );
    }

    /**
     * @return list<array{key: string, label: string, done: bool, url: string}>
     */
    public function forDashboard(Tenant $tenant): array
    {
        return Cache::remember(
            $this->cacheKey($tenant->id, 'dashboard'),
            self::CACHE_TTL_SECONDS,
            fn () => $this->computeDashboardSteps($tenant),
        );
    }

    public function forget(Tenant|int $tenant): void
    {
        $id = $tenant instanceof Tenant ? $tenant->id : $tenant;
        Cache::forget($this->cacheKey($id, 'full'));
        Cache::forget($this->cacheKey($id, 'dashboard'));
    }

    /**
     * @return array{percent: int, completed: int, total: int, steps: list<array{key: string, label: string, done: bool, url: string|null}>}
     */
    private function computeFor(Tenant $tenant): array
    {
        $steps = $this->setupSteps($tenant);
        $completed = collect($steps)->where('done', true)->count();
        $total = count($steps);

        return [
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            'completed' => $completed,
            'total' => $total,
            'steps' => $steps,
        ];
    }

    /**
     * @return list<array{key: string, label: string, done: bool, url: string}>
     */
    private function computeDashboardSteps(Tenant $tenant): array
    {
        $productStats = $tenant->products()
            ->where('is_active', true)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN selling_price IS NULL OR selling_price <= 0 THEN 1 ELSE 0 END) as without_price')
            ->first();

        $productsCount = (int) ($productStats->total ?? 0);
        $productsWithoutPrice = (int) ($productStats->without_price ?? 0);
        $goalAmount = (float) ($tenant->monthlyGoals()
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->value('goal_amount') ?? 0);

        $flags = DB::selectOne('
            SELECT
                EXISTS(
                    SELECT 1 FROM fixed_costs
                    WHERE tenant_id = ? AND is_active = 1 AND deleted_at IS NULL
                ) AS has_costs,
                EXISTS(SELECT 1 FROM sales WHERE tenant_id = ?) AS has_sale
        ', [$tenant->id, $tenant->id]);

        return [
            [
                'key' => 'costs',
                'label' => __('app.setup_progress.steps.costs'),
                'done' => (bool) ($flags->has_costs ?? false),
                'url' => route('tenant.fixed-costs.index'),
            ],
            [
                'key' => 'product',
                'label' => __('app.setup_progress.steps.product'),
                'done' => $productsCount > 0,
                'url' => route('tenant.products.create'),
            ],
            [
                'key' => 'price',
                'label' => __('app.setup_progress.steps.price_all'),
                'done' => $productsCount > 0 && $productsWithoutPrice === 0,
                'url' => route('tenant.products.index', ['unpriced' => 1]),
            ],
            [
                'key' => 'goal',
                'label' => __('app.setup_progress.steps.goal'),
                'done' => $goalAmount > 0,
                'url' => route('tenant.goals.edit'),
            ],
            [
                'key' => 'sale',
                'label' => __('app.setup_progress.steps.sale'),
                'done' => (bool) ($flags->has_sale ?? false),
                'url' => route('tenant.sales.create'),
            ],
        ];
    }

    private function cacheKey(int $tenantId, string $scope): string
    {
        return "tenant_setup_progress:{$tenantId}:{$scope}";
    }

    /**
     * @return list<array{key: string, label: string, done: bool, url: string|null}>
     */
    private function setupSteps(Tenant $tenant): array
    {
        return [
            [
                'key' => 'lgpd',
                'label' => __('app.setup_progress.steps.lgpd'),
                'done' => app(LGPDService::class)->hasRequiredConsents($tenant),
                'url' => route('lgpd.consent'),
            ],
            [
                'key' => 'onboarding',
                'label' => __('app.setup_progress.steps.onboarding'),
                'done' => (bool) $tenant->onboarding_completed,
                'url' => route('onboarding.welcome'),
            ],
            [
                'key' => 'costs',
                'label' => __('app.setup_progress.steps.costs'),
                'done' => $tenant->fixedCosts()->where('is_active', true)->exists(),
                'url' => route('tenant.fixed-costs.index'),
            ],
            [
                'key' => 'product',
                'label' => __('app.setup_progress.steps.product'),
                'done' => $tenant->products()->exists(),
                'url' => route('tenant.products.create'),
            ],
            [
                'key' => 'price',
                'label' => __('app.setup_progress.steps.price'),
                'done' => $tenant->products()->whereNotNull('selling_price')->where('selling_price', '>', 0)->exists(),
                'url' => route('tenant.products.index', ['unpriced' => 1]),
            ],
            [
                'key' => 'sale',
                'label' => __('app.setup_progress.steps.sale'),
                'done' => $tenant->sales()->exists(),
                'url' => route('tenant.sales.create'),
            ],
        ];
    }
}
