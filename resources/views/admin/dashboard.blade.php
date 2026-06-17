<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="__('admin.dashboard.title')" :subtitle="__('admin.dashboard.subtitle')" />
    </x-slot>

        @if(($failedJobsCount ?? 0) > 0)
        <x-ui.alert type="warning" class="mb-6">
            {{ __('admin.dashboard.failed_jobs_alert', ['count' => $failedJobsCount]) }}
            <x-ui.button size="sm" variant="outline" :href="route('admin.failed-jobs.index')" class="ml-2">{{ __('admin.dashboard.failed_jobs_cta') }}</x-ui.button>
        </x-ui.alert>
        @endif

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <x-ui.stat :label="__('admin.dashboard.stats.tenants')" icon="products" accent="blue" :value="(string) $totalTenants" />
            <x-ui.stat :label="__('admin.dashboard.stats.active')" icon="dashboard" accent="brand" :value="(string) $activeTenants" />
            <div title="{{ __('admin.dashboard.stats.mrr_tooltip') }}">
                <x-ui.stat :label="__('admin.dashboard.stats.mrr')" icon="revenue" accent="violet" :value="'R$ '.number_format($mrr, 2, ',', '.')" />
            </div>
            <div title="{{ __('admin.dashboard.stats.churn_tooltip') }}">
                <x-ui.stat :label="__('admin.dashboard.stats.churn')" icon="goals" accent="amber" :value="$churn.'%'" />
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-ui.stat :label="__('admin.dashboard.stats.premium')" icon="spark" accent="violet" :value="(string) $premiumCount" />
            <x-ui.stat :label="__('admin.dashboard.stats.new_this_month')" icon="sales" accent="blue" :value="(string) $newTenantsThisMonth" />
            <x-ui.stat :label="__('admin.dashboard.stats.on_trial')" icon="goals" accent="amber" :value="(string) $onTrialCount" />
            <x-ui.stat :label="__('admin.dashboard.stats.arpu')" icon="revenue" accent="brand" :value="'R$ '.number_format($arpu, 2, ',', '.')" />
        </div>

        <div class="grid lg:grid-cols-3 gap-6 mb-8">
            <x-ui.card class="lg:col-span-2">
                <h3 class="ui-section-title">{{ __('admin.dashboard.quick_access') }}</h3>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach([
                        ['route' => 'admin.tenants.index', 'label' => __('admin.dashboard.links.tenants.label'), 'desc' => __('admin.dashboard.links.tenants.description')],
                        ['route' => 'admin.plans.index', 'label' => __('admin.dashboard.links.plans.label'), 'desc' => __('admin.dashboard.links.plans.description')],
                        ['route' => 'admin.logs.index', 'label' => __('admin.dashboard.links.logs.label'), 'desc' => __('admin.dashboard.links.logs.description')],
                        ['route' => 'admin.lgpd', 'label' => __('admin.dashboard.links.lgpd.label'), 'desc' => __('admin.dashboard.links.lgpd.description')],
                        ['route' => 'admin.failed-jobs.index', 'label' => __('admin.dashboard.links.failed_jobs.label'), 'desc' => __('admin.dashboard.links.failed_jobs.description')],
                    ] as $link)
                    <a href="{{ route($link['route']) }}" class="ui-card-hover p-4 block group">
                        <p class="font-semibold text-ink group-hover:text-brand-dark transition-colors">{{ $link['label'] }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $link['desc'] }}</p>
                    </a>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="ui-section-title">{{ __('admin.dashboard.recent_signups') }}</h3>
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
                    <p class="text-sm text-slate-500">{{ __('admin.dashboard.no_tenants') }}</p>
                    @endforelse
                </ul>
            </x-ui.card>
        </div>

        <p class="text-xs text-slate-400 text-center mb-8">{{ __('admin.dashboard.trial_to_paid', ['rate' => $trialToPaidRate, 'datetime' => now()->format('d/m/Y H:i')]) }}</p>

        <div class="grid lg:grid-cols-2 gap-6 mb-8">
            <x-ui.card>
                <h3 class="ui-section-title">{{ __('admin.dashboard.mrr_trend') }}</h3>
                <div class="space-y-2">
                    @php $maxMrr = max(1, collect($mrrTrend)->max('mrr')); @endphp
                    @foreach($mrrTrend as $point)
                    <div class="flex items-center gap-3 text-sm">
                        <span class="w-14 text-slate-500 shrink-0">{{ $point['month'] }}</span>
                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-violet-500 rounded-full" style="width: {{ ($point['mrr'] / $maxMrr) * 100 }}%"></div>
                        </div>
                        <span class="w-24 text-right tabular-nums">R$ {{ number_format($point['mrr'], 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="ui-section-title">{{ __('admin.dashboard.activation_funnel') }}</h3>
                <ul class="space-y-3 text-sm">
                    @foreach([
                        ['label' => __('admin.dashboard.funnel.registered'), 'value' => $funnel['registered']],
                        ['label' => __('admin.dashboard.funnel.lgpd'), 'value' => $funnel['lgpd']],
                        ['label' => __('admin.dashboard.funnel.onboarded'), 'value' => $funnel['onboarded']],
                        ['label' => __('admin.dashboard.funnel.with_product'), 'value' => $funnel['withProduct']],
                        ['label' => __('admin.dashboard.funnel.with_sale'), 'value' => $funnel['withSale']],
                    ] as $step)
                    <li class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-600">{{ $step['label'] }}</span>
                        <span class="font-semibold tabular-nums">{{ $step['value'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </x-ui.card>
        </div>

        <x-ui.card>
            <h3 class="ui-section-title">{{ __('admin.dashboard.signups_trend') }}</h3>
            <div class="space-y-2">
                @php $maxSignups = max(1, collect($signupTrend)->max('count')); @endphp
                @foreach($signupTrend as $point)
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-14 text-slate-500 shrink-0">{{ $point['month'] }}</span>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-brand rounded-full" style="width: {{ ($point['count'] / $maxSignups) * 100 }}%"></div>
                    </div>
                    <span class="w-8 text-right tabular-nums">{{ $point['count'] }}</span>
                </div>
                @endforeach
            </div>
        </x-ui.card>
</x-app-layout>
