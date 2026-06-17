@extends('layouts.tenant')

@section('title', __('dashboard.title'))
@section('breadcrumb') {{ __('dashboard.breadcrumb') }} @endsection

@section('content')
<x-ui.page-header :title="__('dashboard.greeting', ['name' => $tenant->name])" :subtitle="__('dashboard.subtitle', ['month' => __('sales.months.'.now()->month).' '.now()->year])">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.goals.edit')">{{ __('dashboard.goal') }}</x-ui.button>
        <x-ui.button :href="route('tenant.sales.create')">{{ __('dashboard.new_sale') }}</x-ui.button>
        @if($tenant->isPremium())
        <x-ui.button variant="secondary" :href="route('tenant.reports.monthly')">{{ __('dashboard.export_excel') }}</x-ui.button>
        @endif
    </x-slot:actions>
</x-ui.page-header>

@if(session('guided_setup'))
<x-ui.card class="mb-8 border-brand bg-gradient-to-r from-brand/15 to-brand/5">
    <h2 class="font-display text-xl font-bold text-ink mb-2">{{ __('dashboard.welcome_title') }}</h2>
    <p class="text-sm text-slate-600 mb-4">{{ __('dashboard.welcome_text') }}</p>
    <div class="flex flex-wrap gap-3">
        <x-ui.button :href="route('tenant.products.create')">{{ __('dashboard.create_first_product') }}</x-ui.button>
        <x-ui.button variant="outline" :href="route('tenant.fixed-costs.index')">{{ __('dashboard.review_fixed_costs') }}</x-ui.button>
    </div>
</x-ui.card>
@endif

@if(!$onboardingComplete)
<x-ui.card class="mb-8 border-brand/20 bg-gradient-to-br from-brand/[0.04] to-transparent">
    <h2 class="ui-section-title mb-4">{{ __('dashboard.first_steps') }}</h2>
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

@if($tenant->onTrial())
<x-ui.alert type="warning" class="mb-6">
    {!! __('dashboard.trial_until', ['date' => '<strong>'.$tenant->trial_ends_at->format('d/m/Y').'</strong>']) !!}
    <a href="{{ route('tenant.billing.upgrade') }}" class="underline font-semibold ml-1">{{ __('dashboard.upgrade') }}</a>
</x-ui.alert>
@elseif(!$tenant->isPremium() && $tenant->trial_ends_at && $tenant->trial_ends_at->isPast())
<x-ui.alert type="warning" class="mb-6">
    {{ __('dashboard.trial_ended') }}
    <a href="{{ route('tenant.billing.upgrade') }}" class="underline font-semibold">{{ __('dashboard.activate_premium') }}</a>
    {{ __('dashboard.trial_ended_suffix') }}
</x-ui.alert>
@endif

@if($productsWithoutPrice > 0)
<x-ui.alert type="warning" class="mb-6">
    <strong>{{ $productsWithoutPrice }}</strong> {{ __('dashboard.products_without_price') }}
    <a href="{{ route('tenant.products.index') }}" class="font-semibold underline ml-1">{{ __('dashboard.price_now') }}</a>
</x-ui.alert>
@endif

<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-5 mb-8 [&>div]:shadow-sm">
    <x-ui.stat :label="__('dashboard.revenue_month')" icon="revenue" accent="brand"
        :value="'R$ '.number_format($monthRevenue, 2, ',', '.')"
        :trend="$goalAmount > 0 ? __('dashboard.goal_percent', ['percent' => number_format($goalProgress, 0)]) : ''" />
    <x-ui.stat :label="__('dashboard.active_products')" icon="products" accent="blue" :value="(string) $productsCount" />
    <x-ui.stat :label="__('dashboard.sales_month')" icon="sales" accent="violet" :value="(string) $salesCount" />
    <x-ui.stat :label="__('dashboard.monthly_goal')" icon="goals" accent="amber"
        :value="'R$ '.number_format($goalAmount, 2, ',', '.')"
        :trend="$goalAmount > 0 ? __('dashboard.goal_progress', ['percent' => number_format(min(100, $goalProgress), 0)]) : __('dashboard.set_goal')" />
</div>

@if($goalAmount > 0)
<div class="ui-card p-4 mb-8">
    <div class="flex justify-between text-sm mb-2">
        <span class="font-medium text-slate-600">{{ __('dashboard.goal_progress_title') }}</span>
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
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">{{ __('dashboard.insight_title') }}</p>
            <p class="text-slate-700 leading-relaxed text-sm">{{ $aiTip }}</p>
        </div>
    </div>
</x-ui.card>
@endif

<div class="grid lg:grid-cols-3 gap-5 mb-8">
    <x-ui.card class="lg:col-span-2">
        <x-slot:header>
            <h2 class="ui-section-title mb-0">{{ __('dashboard.revenue_chart') }}</h2>
        </x-slot:header>
        <canvas id="revenueChart" height="100"></canvas>
    </x-ui.card>
    <x-ui.card>
        <x-slot:header>
            <div class="flex items-center justify-between gap-2">
                <h2 class="ui-section-title mb-0">{{ __('dashboard.payments') }}</h2>
                <span class="text-xs text-slate-400">{{ __('dashboard.current_month') }}</span>
            </div>
        </x-slot:header>
        <canvas id="paymentChart" height="200"></canvas>
        @if($paymentSalesTotal === 0)
        <p class="text-center text-sm text-slate-400 mt-3">
            {{ __('dashboard.no_sales_month') }}
            <a href="{{ route('tenant.sales.create') }}" class="font-semibold text-brand-dark hover:underline">{{ __('dashboard.register_sale') }} →</a>
        </p>
        @endif
    </x-ui.card>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <x-ui.card>
        <x-slot:header>
            <h2 class="ui-section-title mb-0">{{ __('dashboard.top_products') }}</h2>
        </x-slot:header>
        <canvas id="topProductsChart" height="160"></canvas>
    </x-ui.card>
    <x-ui.card :padding="false" class="overflow-hidden">
        <x-slot:header>
            <h2 class="ui-section-title mb-0">{{ __('dashboard.recent_sales') }}</h2>
        </x-slot:header>
        <div class="overflow-x-auto">
            <table class="ui-table">
                <thead><tr><th>{{ __('dashboard.product') }}</th><th>{{ __('dashboard.total') }}</th><th>{{ __('dashboard.date') }}</th></tr></thead>
                <tbody>
                @forelse($recentSales as $sale)
                <tr>
                    <td class="font-medium">{{ $sale->product?->name }}</td>
                    <td class="text-brand-dark font-semibold">R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                    <td class="text-slate-500">{{ $sale->sold_at->format('d/m') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-slate-400 py-8">{{ __('dashboard.no_sales_yet') }} <a href="{{ route('tenant.sales.create') }}" class="text-brand-dark font-medium">{{ __('dashboard.register_sale') }} →</a></td></tr>
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
