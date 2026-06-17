<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ProfitMargin;
use App\Models\Plan;
use App\Models\Tenant;

class PlanLimitService
{
    public function maxProducts(Tenant $tenant): ?int
    {
        if ($tenant->isPremium()) {
            return null;
        }

        $plan = $this->resolvePlan($tenant);
        if ($plan !== null && $plan->max_products !== null) {
            return (int) $plan->max_products;
        }

        return (int) config('tenancy.basic_max_products', 5);
    }

    public function currentProductCount(Tenant $tenant): int
    {
        return $tenant->products()->count();
    }

    public function canCreateProduct(Tenant $tenant): bool
    {
        $max = $this->maxProducts($tenant);

        if ($max === null) {
            return true;
        }

        return $this->currentProductCount($tenant) < $max;
    }

    public function productLimitMessage(Tenant $tenant): string
    {
        $max = $this->maxProducts($tenant) ?? 0;

        return __('messages.plan.product_limit', ['max' => $max]);
    }

    /** @return list<float> */
    public function allowedMargins(Tenant $tenant): array
    {
        $plan = $tenant->isPremium() ? 'premium' : 'basic';

        return array_map(
            fn (ProfitMargin $margin) => (float) $margin->value,
            ProfitMargin::forPlan($plan)
        );
    }

    public function isMarginAllowed(Tenant $tenant, float $margin): bool
    {
        return in_array($margin, $this->allowedMargins($tenant), true);
    }

    private function resolvePlan(Tenant $tenant): ?Plan
    {
        $slug = $tenant->plan?->value ?? (string) $tenant->plan;

        return Plan::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }
}
