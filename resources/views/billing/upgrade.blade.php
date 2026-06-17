@extends('layouts.tenant')
@section('title', __('billing.title_upgrade'))
@section('breadcrumb') {{ __('billing.breadcrumb') }} @endsection
@section('analytics_page', 'billing_upgrade')

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
@php
    $premiumPrice = number_format($plan?->price_monthly ?? 29.90, 2, ',', '.');
    $premiumFeatures = $plan?->features ?? __('billing.premium_features');
    $trialDaysLeft = ($tenant->onTrial() && $tenant->trial_ends_at?->isFuture())
        ? (int) now()->diffInDays($tenant->trial_ends_at)
        : null;
@endphp
<div class="max-w-4xl mx-auto py-6 md:py-10 animate-fade-in">
    @if($tenant->onTrial() && $tenant->trial_ends_at)
    <x-ui.alert type="warning" class="mb-8">
        @if($trialDaysLeft !== null && $trialDaysLeft <= 14)
        <span class="font-semibold">{{ __('billing.trial_days_left', ['days' => $trialDaysLeft]) }}</span> —
        @endif
        {!! __('billing.trial_banner', ['date' => '<strong>'.$tenant->trial_ends_at->format('d/m/Y').'</strong>']) !!}
    </x-ui.alert>
    @endif

    <x-ui.page-header :title="__('billing.unlock_title')" :subtitle="__('billing.unlock_subtitle')" class="mb-10 md:mb-12 text-center [&>div]:mx-auto" />
    <p class="text-xs text-slate-400 text-center -mt-6 mb-10">{{ __('billing.social_proof') }}</p>

    <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
        <x-ui.card class="border border-slate-200/80 bg-slate-50/30">
            <p class="text-xs font-bold uppercase text-slate-400 tracking-wide">{{ __('billing.current_plan') }}</p>
            <h2 class="font-display text-xl font-bold mt-2">{{ __('billing.basic') }}</h2>
            <p class="text-3xl font-bold mt-2 tabular-nums">{{ __('billing.free') }}</p>
            <ul class="mt-6 space-y-3 text-sm text-slate-600">
                @foreach(__('billing.basic_features') as $feature)
                <li class="flex gap-3 items-start">
                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-200/80 text-slate-600 text-xs">✓</span>
                    <span>{{ $feature }}</span>
                </li>
                @endforeach
            </ul>
        </x-ui.card>

        <x-ui.card class="border-2 border-brand relative overflow-hidden shadow-premium-glow ring-4 ring-brand/10 bg-gradient-to-b from-white to-brand/[0.03]">
            <span class="absolute top-4 right-4 ui-badge-premium">{{ __('billing.recommended') }}</span>
            <p class="text-xs font-bold uppercase text-brand-dark tracking-wide">{{ __('billing.premium') }}</p>
            <h2 class="font-display text-xl font-bold mt-2">{{ __('billing.premium') }}</h2>
            <p class="text-3xl font-bold mt-2 tabular-nums">
                R$ {{ $premiumPrice }}<span class="text-base font-normal text-slate-500">{{ __('billing.per_month') }}</span>
            </p>
            <ul class="mt-6 space-y-3 text-sm text-slate-700">
                @foreach($premiumFeatures as $feature)
                <li class="flex gap-3 items-start">
                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand/15 text-brand-dark text-xs font-bold">✓</span>
                    <span>{{ is_string($feature) ? $feature : $feature }}</span>
                </li>
                @endforeach
            </ul>
            <div class="flex flex-col gap-3 mt-8">
                <form method="POST" action="{{ route('tenant.billing.stripe') }}">@csrf
                    <x-ui.button type="submit" variant="secondary" class="w-full py-3.5 text-base shadow-sm">{{ __('billing.credit_card') }}</x-ui.button>
                </form>
                <x-ui.button variant="outline" :href="route('tenant.billing.pix')" class="w-full py-3.5">{{ __('billing.pix') }}</x-ui.button>
            </div>
            <p class="text-center text-xs text-slate-400 mt-4">{{ __('billing.guarantee') }}</p>
        </x-ui.card>
    </div>
</div>
@endif
@endsection
