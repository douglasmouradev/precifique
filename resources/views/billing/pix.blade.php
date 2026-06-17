@extends('layouts.tenant')
@section('title', __('billing.pix_page.title'))
@section('breadcrumb') {{ __('billing.pix_page.breadcrumb') }} @endsection
@section('analytics_page', 'billing_pix')

@section('content')
<x-ui.page-header :title="__('billing.pix_page.heading')" :subtitle="__('billing.pix_page.amount', ['amount' => 'R$ '.number_format($plan->price_monthly ?? 0, 2, ',', '.')])">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.billing.upgrade')">{{ __('billing.breadcrumb') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<div
    id="billing-pix-page"
    class="max-w-lg mx-auto animate-fade-in"
    data-premium="{{ current_tenant()?->isPremium() ? '1' : '0' }}"
    data-status-url="{{ route('tenant.billing.pix.status') }}"
    data-dashboard-url="{{ route('tenant.dashboard') }}"
    data-toast-message="{{ __('billing.pix_page.toast_confirmed') }}"
    data-copy-label="{{ __('billing.pix_page.copy_paste') }}"
    data-copied-label="{{ __('billing.copied') }}"
>
    <ol class="flex items-center gap-2 mb-8 text-xs font-medium">
        <li class="flex items-center gap-2 text-brand-dark"><span class="w-6 h-6 rounded-full bg-brand text-ink flex items-center justify-center text-[11px] font-bold">1</span>{{ __('billing.pix_page.step_scan') }}</li>
        <li class="w-6 h-px bg-slate-200"></li>
        <li class="flex items-center gap-2 text-slate-500"><span class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[11px] font-bold">2</span>{{ __('billing.pix_page.step_pay') }}</li>
        <li class="w-6 h-px bg-slate-200"></li>
        <li class="flex items-center gap-2 text-slate-500"><span class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[11px] font-bold">3</span>{{ __('billing.pix_page.step_done') }}</li>
    </ol>

    @if(isset($pix['error']))
    <x-ui.alert type="warning" class="text-left">{{ $pix['error'] }}</x-ui.alert>
    @else
    <x-ui.card class="text-left overflow-hidden">
        <div class="bg-gradient-to-br from-brand/5 to-transparent -mx-5 -mt-5 px-5 pt-5 pb-4 mb-4 border-b border-slate-100">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('billing.pix_page.scan_title') }}</p>
            <p class="text-2xl font-display font-bold text-ink mt-1 tabular-nums">R$ {{ number_format($plan->price_monthly ?? 0, 2, ',', '.') }}</p>
        </div>
        @if(!empty($pix['qr_code_base64']))
        <div class="relative mx-auto mb-6 w-fit">
            <img src="data:image/png;base64,{{ $pix['qr_code_base64'] }}" alt="{{ __('billing.pix_page.qr_alt') }}" class="rounded-2xl ring-2 ring-brand/20 shadow-premium-glow max-w-[220px]" width="220" height="220">
            <p class="text-center text-xs text-slate-400 mt-3" data-pix-timer>{{ __('billing.pix_page.expires_hint') }}</p>
        </div>
        @endif
        @if(!empty($pix['qr_code']))
        <label class="ui-label">{{ __('billing.pix_page.copy_paste_label') }}</label>
        <textarea readonly id="pix-copy-code" class="ui-input text-xs font-mono mb-3" rows="3">{{ $pix['qr_code'] }}</textarea>
        <x-ui.button type="button" variant="secondary" class="w-full mb-4" data-pix-copy>{{ __('billing.pix_page.copy_paste') }}</x-ui.button>
        <p class="text-sm text-slate-500 @if(current_tenant()?->isPremium()) hidden @endif" data-pix-waiting>{{ __('billing.pix_page.waiting_activation') }}</p>
        <p class="text-sm text-emerald-600 font-medium @unless(current_tenant()?->isPremium()) hidden @endunless" data-pix-confirmed>{{ __('billing.pix_page.payment_confirmed') }}</p>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-2 @if(current_tenant()?->isPremium()) hidden @endif" data-pix-checking>
            <span class="inline-block w-2 h-2 rounded-full bg-brand animate-pulse"></span>
            {{ __('billing.pix_page.checking_payment') }}
        </p>
        @endif
    </x-ui.card>
    @endif

    <x-ui.button variant="ghost" :href="route('tenant.dashboard')" class="mt-8 w-full justify-center">{{ __('billing.pix_page.back_dashboard') }}</x-ui.button>
</div>
@endsection
