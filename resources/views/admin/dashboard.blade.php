<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-ink">Visão geral do SaaS</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <x-ui.stat label="Tenants" icon="products" accent="blue" :value="(string) $totalTenants" />
            <x-ui.stat label="Ativos" icon="dashboard" accent="brand" :value="(string) $activeTenants" />
            <div title="Receita recorrente mensal das assinaturas ativas.">
                <x-ui.stat label="MRR" icon="revenue" accent="violet" :value="'R$ '.number_format($mrr, 2, ',', '.')" />
            </div>
            <div title="Percentual de cancelamentos de assinatura no mês atual.">
                <x-ui.stat label="Churn" icon="goals" accent="amber" :value="$churn.'%'" />
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-ui.stat label="Premium" icon="spark" accent="violet" :value="(string) $premiumCount" />
            <x-ui.stat label="Novos no mês" icon="sales" accent="blue" :value="(string) $newTenantsThisMonth" />
            <x-ui.stat label="Em trial" icon="goals" accent="amber" :value="(string) $onTrialCount" />
            <x-ui.stat label="ARPU estimado" icon="revenue" accent="brand" :value="'R$ '.number_format($arpu, 2, ',', '.')" />
        </div>

        <div class="grid lg:grid-cols-3 gap-6 mb-8">
            <x-ui.card class="lg:col-span-2">
                <h3 class="ui-section-title">Acesso rápido</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach([
                        ['route' => 'admin.tenants.index', 'label' => 'Gerenciar tenants', 'desc' => 'Contas, planos e status'],
                        ['route' => 'admin.plans.index', 'label' => 'Planos e preços', 'desc' => 'Stripe e valores mensais'],
                        ['route' => 'admin.logs.index', 'label' => 'Logs e auditoria', 'desc' => 'Rastreabilidade de ações'],
                        ['route' => 'admin.lgpd', 'label' => 'Conformidade LGPD', 'desc' => 'Consentimentos registrados'],
                    ] as $link)
                    <a href="{{ route($link['route']) }}" class="ui-card-hover p-4 block group">
                        <p class="font-semibold text-ink group-hover:text-brand-dark transition-colors">{{ $link['label'] }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $link['desc'] }}</p>
                    </a>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="ui-section-title">Últimos cadastros</h3>
                <ul class="space-y-3">
                    @forelse($recentTenants as $t)
                    <li class="flex items-start justify-between gap-2 text-sm border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                        <div class="min-w-0">
                            <p class="font-medium text-ink truncate">{{ $t->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $t->email }}</p>
                        </div>
                        <span class="ui-badge-brand shrink-0">{{ $t->plan?->value ?? $t->plan }}</span>
                    </li>
                    @empty
                    <p class="text-sm text-slate-500">Nenhum tenant ainda.</p>
                    @endforelse
                </ul>
            </x-ui.card>
        </div>

        <p class="text-xs text-slate-400 text-center">Taxa trial→pago (estimada): {{ $trialToPaidRate }}% · Atualizado {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</x-app-layout>
