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
        $steps = [
            [
                'key' => 'lgpd',
                'label' => 'Aceitar termos e privacidade',
                'done' => app(LGPDService::class)->hasRequiredConsents($tenant),
                'url' => route('lgpd.consent'),
            ],
            [
                'key' => 'profile',
                'label' => 'Montar perfil e nicho',
                'done' => (bool) $tenant->profile_setup_completed,
                'url' => route('tenant.profile.setup'),
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
                'url' => route('tenant.products.index'),
            ],
            [
                'key' => 'sale',
                'label' => 'Registrar primeira venda',
                'done' => $tenant->sales()->exists(),
                'url' => route('tenant.sales.create'),
            ],
        ];

        $completed = collect($steps)->where('done', true)->count();
        $total = count($steps);
        $percent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return [
            'percent' => $percent,
            'completed' => $completed,
            'total' => $total,
            'steps' => $steps,
        ];
    }
}
