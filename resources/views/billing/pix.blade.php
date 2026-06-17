@extends('layouts.tenant')
@section('title', __('billing.pix_page.title'))
@section('breadcrumb') {{ __('billing.pix_page.breadcrumb') }} @endsection

@section('content')
<div
    id="billing-pix-page"
    class="max-w-lg mx-auto text-center py-6 animate-fade-in"
    data-premium="{{ current_tenant()?->isPremium() ? '1' : '0' }}"
    data-status-url="{{ route('tenant.billing.pix.status') }}"
    data-dashboard-url="{{ route('tenant.dashboard') }}"
    data-toast-message="{{ __('billing.pix_page.toast_confirmed') }}"
    data-copy-label="{{ __('billing.pix_page.copy_paste') }}"
    data-copied-label="{{ __('billing.copied') }}"
>
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand/15 text-brand-dark mb-6 ring-1 ring-brand/20">
        <x-ui.nav-icon name="sales" class="w-8 h-8" />
    </div>
    <h1 class="ui-page-title">{{ __('billing.pix_page.heading') }}</h1>

    @if(isset($pix['error']))
    <x-ui.alert type="warning" class="mt-6 text-left">{{ $pix['error'] }}</x-ui.alert>
    @else
    <p class="ui-page-subtitle mt-2">{{ __('billing.pix_page.amount', ['amount' => 'R$ '.number_format($plan->price_monthly, 2, ',', '.')]) }}</p>

    <x-ui.card class="mt-8 text-left">
        @if(!empty($pix['qr_code_base64']))
        <img src="data:image/png;base64,{{ $pix['qr_code_base64'] }}" alt="{{ __('billing.pix_page.qr_alt') }}" class="mx-auto mb-6 max-w-[220px] rounded-xl ring-1 ring-slate-200 shadow-sm" width="220" height="220">
        @endif
        @if(!empty($pix['qr_code']))
        <label class="ui-label">{{ __('billing.pix_page.copy_paste_label') }}</label>
        <textarea readonly id="pix-copy-code" class="ui-input text-xs font-mono mb-3" rows="4">{{ $pix['qr_code'] }}</textarea>
        <x-ui.button type="button" variant="secondary" class="w-full mb-4" data-pix-copy>{{ __('billing.pix_page.copy_paste') }}</x-ui.button>
        <p class="text-sm text-slate-500 @if(current_tenant()?->isPremium()) hidden @endif" data-pix-waiting>{{ __('billing.pix_page.waiting_activation') }}</p>
        <p class="text-sm text-emerald-600 font-medium @unless(current_tenant()?->isPremium()) hidden @endunless" data-pix-confirmed>{{ __('billing.pix_page.payment_confirmed') }}</p>
        <p class="text-xs text-slate-400 mt-2 @if(current_tenant()?->isPremium()) hidden @endif" data-pix-checking>{{ __('billing.pix_page.checking_payment') }}</p>
        @endif
    </x-ui.card>
    @endif

    <x-ui.button variant="ghost" :href="route('tenant.dashboard')" class="mt-8">{{ __('billing.pix_page.back_dashboard') }}</x-ui.button>
</div>
@endsection
