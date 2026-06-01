@extends('layouts.tenant')

@section('title', 'Dashboard')
@section('breadcrumb') Dashboard @endsection

@section('content')
<x-ui.page-header title="Olá, {{ $tenant->name }}" :subtitle="'Resumo de '.now()->translatedFormat('F Y')">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.goals.edit')">Meta</x-ui.button>
        <x-ui.button :href="route('tenant.sales.create')">Nova venda</x-ui.button>
        @if($tenant->isPremium())
        <x-ui.button variant="secondary" :href="route('tenant.reports.monthly')">Exportar Excel</x-ui.button>
        @endif
    </x-slot:actions>
</x-ui.page-header>

@if(session('guided_setup'))
<x-ui.card class="mb-8 border-brand bg-gradient-to-r from-brand/15 to-brand/5">
    <h2 class="font-display text-xl font-bold text-ink mb-2">Bem-vindo ao Precifique!</h2>
    <p class="text-sm text-slate-600 mb-4">Siga os passos abaixo para calcular seu primeiro preço com confiança.</p>
    <div class="flex flex-wrap gap-3">
        <x-ui.button :href="route('tenant.products.create')">Criar primeiro produto</x-ui.button>
        <x-ui.button variant="outline" :href="route('tenant.fixed-costs.index')">Revisar custos fixos</x-ui.button>
    </div>
</x-ui.card>
@endif

@if(!$onboardingComplete)
<x-ui.card class="mb-8 border-brand/20 bg-gradient-to-br from-brand/[0.04] to-transparent">
    <h2 class="ui-section-title mb-4">Primeiros passos</h2>
    <ul class="space-y-3">
        @foreach($onboardingSteps as $step)
        <li class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $step['done'] ? 'bg-brand text-ink' : 'bg-slate-100 text-slate-400' }}">
                {{ $step['done'] ? '✓' : '·' }}
            </span>
            @if($step['done'])
            <span class="text-slate-500 line-through text-sm">{{ $step['label'] }}</span>
            @else
            <a href="{{ $step['url'] }}" class="text-sm font-semibold text-brand-dark hover:underline">{{ $step['label'] }} →</a>
            @endif
        </li>
        @endforeach
    </ul>
</x-ui.card>
@endif

@if($productsWithoutPrice > 0)
<x-ui.alert type="warning" class="mb-6">
    <strong>{{ $productsWithoutPrice }}</strong> produto(s) ainda sem preço de venda.
    <a href="{{ route('tenant.products.index') }}" class="font-semibold underline ml-1">Precificar agora</a>
</x-ui.alert>
@endif

<div
    class="mb-8"
    x-data="{ ready: true }"
    x-init="ready = false; requestAnimationFrame(() => { ready = true })"
>
    <div x-show="!ready" x-cloak class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-5 mb-4">
        @for ($i = 0; $i < 4; $i++)
        <x-ui.skeleton-stat />
        @endfor
    </div>
    <div x-show="ready" class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-5 [&>div]:shadow-sm">
    <x-ui.stat label="Faturamento do mês" icon="revenue" accent="brand"
        :value="'R$ '.number_format($monthRevenue, 2, ',', '.')"
        :trend="$goalAmount > 0 ? number_format($goalProgress, 0).'% da meta' : ''" />
    <x-ui.stat label="Produtos ativos" icon="products" accent="blue" :value="(string) $productsCount" />
    <x-ui.stat label="Vendas do mês" icon="sales" accent="violet" :value="(string) $salesCount" />
    <x-ui.stat label="Meta mensal" icon="goals" accent="amber"
        :value="'R$ '.number_format($goalAmount, 2, ',', '.')"
        :trend="$goalAmount > 0 ? 'Progresso: '.number_format(min(100, $goalProgress), 0).'%' : 'Defina sua meta'" />
    </div>
</div>

@if($goalAmount > 0)
<div class="ui-card p-4 mb-8">
    <div class="flex justify-between text-sm mb-2">
        <span class="font-medium text-slate-600">Progresso da meta</span>
        <span class="font-semibold text-brand-dark">{{ number_format(min(100, $goalProgress), 0) }}%</span>
    </div>
    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full bg-gradient-to-r from-brand to-brand-dark rounded-full transition-all duration-700" style="width: {{ min(100, $goalProgress) }}%"></div>
    </div>
</div>
@endif

@if($aiTip)
<x-ui.card class="mb-8">
    <div class="flex gap-4">
        <div class="ui-stat-icon bg-brand/10 text-brand-dark ring-1 ring-brand/20 shrink-0">
            <x-ui.nav-icon name="spark" class="w-5 h-5" />
        </div>
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Insight do dia</p>
            <p class="text-slate-700 leading-relaxed text-sm">{{ $aiTip }}</p>
        </div>
    </div>
</x-ui.card>
@endif

<div class="grid lg:grid-cols-3 gap-5 mb-8">
    <x-ui.card class="lg:col-span-2">
        <x-slot:header>
            <h2 class="ui-section-title mb-0">Faturamento — 6 meses</h2>
        </x-slot:header>
        <canvas id="revenueChart" height="100"></canvas>
    </x-ui.card>
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between gap-2">
                <h2 class="ui-section-title mb-0">Pagamentos</h2>
                <span class="text-xs text-slate-400">mês atual</span>
            </div>
        </x-slot:header>
        <canvas id="paymentChart" height="200"></canvas>
        @if($paymentSalesTotal === 0)
        <p class="text-center text-sm text-slate-400 mt-3">
            Nenhuma venda neste mês.
            <a href="{{ route('tenant.sales.create') }}" class="font-semibold text-brand-dark hover:underline">Registrar →</a>
        </p>
        @endif
    </x-ui.card>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <x-ui.card>
        <x-slot:header>
            <h2 class="ui-section-title mb-0">Top produtos</h2>
        </x-slot:header>
        <canvas id="topProductsChart" height="160"></canvas>
    </x-ui.card>
    <x-ui.card :padding="false" class="overflow-hidden">
        <x-slot:header>
            <h2 class="ui-section-title mb-0">Últimas vendas</h2>
        </x-slot:header>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead><tr><th>Produto</th><th>Total</th><th>Data</th></tr></thead>
                <tbody>
                @forelse($recentSales as $sale)
                <tr>
                    <td class="font-medium">{{ $sale->product?->name }}</td>
                    <td class="text-brand-dark font-semibold">R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                    <td class="text-slate-500">{{ $sale->sold_at->format('d/m') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-slate-400 py-8">Nenhuma venda ainda. <a href="{{ route('tenant.sales.create') }}" class="text-brand-dark font-medium">Registrar →</a></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
@endsection

@push('scripts')
@vite('resources/js/dashboard-charts.js')
<script>
window.dashboardChartData = {
    revenueLabels: @json($revenueChartLabels),
    revenueTotals: @json($revenueChartTotals),
    paymentLabels: @json($paymentLabels),
    paymentCounts: @json($paymentCounts),
    paymentColors: @json($paymentColors),
    paymentSalesTotal: {{ (int) $paymentSalesTotal }},
    topProductLabels: @json($topProductLabels),
    topProductQty: @json($topProductQty),
};
</script>
@endpush
