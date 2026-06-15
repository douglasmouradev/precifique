<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;

class TenantSetupProgressService
{
    /**
     * @return array{percent: int, completed: int, total: int, steps: list<array{key: string, label: string, done: bool, url: string|null}>}
     */
    public function for(Tenant $tenant): array
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
     * Passos operacionais exibidos no dashboard (sem LGPD/onboarding inicial).
     *
     * @return list<array{key: string, label: string, done: bool, url: string}>
     */
    public function forDashboard(Tenant $tenant): array
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

        return [
            [
                'key' => 'costs',
                'label' => 'Cadastrar custos fixos',
                'done' => $tenant->fixedCosts()->where('is_active', true)->exists(),
                'url' => route('tenant.fixed-costs.index'),
            ],
            [
                'key' => 'product',
                'label' => 'Criar primeiro produto',
                'done' => $productsCount > 0,
                'url' => route('tenant.products.create'),
            ],
            [
                'key' => 'price',
                'label' => 'Precificar todos os produtos',
                'done' => $productsCount > 0 && $productsWithoutPrice === 0,
                'url' => route('tenant.products.index', ['unpriced' => 1]),
            ],
            [
                'key' => 'goal',
                'label' => 'Definir meta mensal',
                'done' => $goalAmount > 0,
                'url' => route('tenant.goals.edit'),
            ],
            [
                'key' => 'sale',
                'label' => 'Registrar primeira venda',
                'done' => $tenant->sales()->exists(),
                'url' => route('tenant.sales.create'),
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string, done: bool, url: string|null}>
     */
    private function setupSteps(Tenant $tenant): array
    {
        return [
            [
                'key' => 'lgpd',
                'label' => 'Aceitar termos e privacidade',
                'done' => app(LGPDService::class)->hasRequiredConsents($tenant),
                'url' => route('lgpd.consent'),
            ],
            [
                'key' => 'onboarding',
                'label' => 'Concluir configuração inicial',
                'done' => (bool) $tenant->onboarding_completed,
                'url' => route('onboarding.welcome'),
            ],
            [
                'key' => 'costs',
                'label' => 'Cadastrar custos fixos',
                'done' => $tenant->fixedCosts()->where('is_active', true)->exists(),
                'url' => route('tenant.fixed-costs.index'),
            ],
            [
                'key' => 'product',
                'label' => 'Criar primeiro produto',
                'done' => $tenant->products()->exists(),
                'url' => route('tenant.products.create'),
            ],
            [
                'key' => 'price',
                'label' => 'Precificar um produto',
                'done' => $tenant->products()->whereNotNull('selling_price')->where('selling_price', '>', 0)->exists(),
                'url' => route('tenant.products.index', ['unpriced' => 1]),
            ],
            [
                'key' => 'sale',
                'label' => 'Registrar primeira venda',
                'done' => $tenant->sales()->exists(),
                'url' => route('tenant.sales.create'),
            ],
        ];
    }
}
