@props(['tenant' => null])

@php
    $tenant = $tenant ?? auth('tenant')->user();
@endphp

@if($tenant && ! $tenant->isPremium())
<div {{ $attributes->merge(['class' => 'mb-6 rounded-2xl border border-brand/25 bg-gradient-to-r from-brand/5 via-white to-brand/5 px-4 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm']) }} role="status">
    <p class="text-sm text-slate-700">{{ __('billing.inline_upgrade_banner') }}</p>
    <x-ui.button size="sm" :href="route('tenant.billing.upgrade')" class="shrink-0">{{ __('app.account.upgrade') }}</x-ui.button>
</div>
@endif
