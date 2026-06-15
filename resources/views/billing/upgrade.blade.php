@extends('layouts.tenant')
@section('title', __('billing.title_upgrade'))
@section('breadcrumb') {{ __('billing.breadcrumb') }} @endsection

@section('content')
@if($tenant->isPremium())
<div class="max-w-4xl mx-auto py-8">
    <x-ui.page-header :title="__('billing.premium_active_title')" :subtitle="__('billing.premium_active_subtitle')">
        <x-slot:actions>
            @if($tenant->subscription?->stripe_subscription_id)
            <x-ui.button :href="route('tenant.billing.portal')">{{ __('billing.manage_stripe') }}</x-ui.button>
            @endif
            <x-ui.button variant="outline" :href="route('tenant.account.index')">{{ __('billing.my_account') }}</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>
    <x-ui.alert type="success">{{ __('billing.premium_access') }}</x-ui.alert>
</div>
@else
<div class="max-w-4xl mx-auto py-8">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-brand/20 to-amber-100 mb-6 ring-1 ring-brand/20">
            <x-ui.nav-icon name="spark" class="w-8 h-8 text-brand-dark" />
        </div>
        <h1 class="ui-page-title text-balance">{{ __('billing.unlock_title') }}</h1>
        <p class="ui-page-subtitle mt-3 max-w-lg mx-auto">
            {{ __('billing.unlock_subtitle') }}
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
        <x-ui.card class="border-2 border-slate-200">
            <p class="text-xs font-bold uppercase text-slate-400 tracking-wide">{{ __('billing.current_plan') }}</p>
            <h2 class="font-display text-xl font-bold mt-2">{{ __('billing.basic') }}</h2>
            <p class="text-3xl font-bold mt-2">{{ __('billing.free') }}</p>
            <ul class="mt-6 space-y-2 text-sm text-slate-600">
                <li class="flex gap-2"><span class="text-brand">✓</span> {{ __('billing.basic_products') }}</li>
                <li class="flex gap-2"><span class="text-brand">✓</span> {{ __('billing.basic_margins') }}</li>
            </ul>
        </x-ui.card>

        <x-ui.card class="border-2 border-brand ring-4 ring-brand/10 relative overflow-hidden shadow-premium-glow">
            <span class="absolute top-4 right-4 ui-badge-premium">{{ __('billing.recommended') }}</span>
            <p class="text-xs font-bold uppercase text-brand-dark tracking-wide">{{ __('billing.premium') }}</p>
            <h2 class="font-display text-xl font-bold mt-2">{{ __('billing.premium') }}</h2>
            <p class="text-3xl font-bold mt-2">R$ {{ number_format($plan?->price_monthly ?? 29.90, 2, ',', '.') }}<span class="text-base font-normal text-slate-500">{{ __('billing.per_month') }}</span></p>
            <ul class="mt-6 space-y-2 text-sm text-slate-600">
                @foreach($plan?->features ?? ['Produtos ilimitados', 'IA integrada', 'Relatório Excel', '5 margens de lucro'] as $f)
                <li class="flex gap-2"><span class="text-brand">✓</span> {{ is_string($f) ? $f : $f }}</li>
                @endforeach
            </ul>
            <div class="flex flex-col gap-3 mt-8">
                <form method="POST" action="{{ route('tenant.billing.stripe') }}">@csrf
                    <x-ui.button type="submit" variant="secondary" class="w-full py-3">{{ __('billing.credit_card') }}</x-ui.button>
                </form>
                <x-ui.button variant="outline" :href="route('tenant.billing.pix')" class="w-full py-3">{{ __('billing.pix') }}</x-ui.button>
            </div>
        </x-ui.card>
    </div>
</div>
@endif
@endsection
