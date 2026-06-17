@props([
    'tenant',
    'onTrial' => false,
    'trialEndsAt' => null,
    'productsWithoutPrice' => 0,
    'onboardingComplete' => true,
    'maxProducts' => null,
    'productCount' => 0,
])

@php
    $alerts = [];
    if ($onTrial && $trialEndsAt) {
        $alerts[] = [
            'type' => 'warning',
            'message' => __('dashboard.trial_until', ['date' => '<strong>'.$trialEndsAt->format('d/m/Y').'</strong>']),
            'action' => ['label' => __('dashboard.upgrade'), 'url' => route('tenant.billing.upgrade')],
        ];
    } elseif (! $tenant->isPremium() && $tenant->trial_ends_at?->isPast()) {
        $alerts[] = [
            'type' => 'warning',
            'message' => __('dashboard.trial_ended').' '.__('dashboard.trial_ended_suffix'),
            'action' => ['label' => __('dashboard.activate_premium'), 'url' => route('tenant.billing.upgrade')],
        ];
    }
    if ($maxProducts && $productCount >= $maxProducts) {
        $alerts[] = [
            'type' => 'warning',
            'message' => __('dashboard.product_limit_reached', ['count' => $productCount, 'max' => $maxProducts]),
            'action' => ['label' => __('dashboard.upgrade'), 'url' => route('tenant.billing.upgrade')],
        ];
    } elseif ($maxProducts && $productCount >= max(1, (int) floor($maxProducts * 0.8))) {
        $alerts[] = [
            'type' => 'info',
            'message' => __('dashboard.product_limit_near', ['count' => $productCount, 'max' => $maxProducts]),
            'action' => ['label' => __('dashboard.upgrade'), 'url' => route('tenant.billing.upgrade')],
        ];
    }
    if ($productsWithoutPrice > 0) {
        $alerts[] = [
            'type' => 'info',
            'message' => '<strong>'.$productsWithoutPrice.'</strong> '.__('dashboard.products_without_price'),
            'action' => ['label' => __('dashboard.price_now'), 'url' => route('tenant.products.index', ['unpriced' => 1])],
        ];
    }
    $primary = $alerts[0] ?? null;
@endphp

@if($primary)
<div class="mb-6 rounded-2xl border px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 {{ $primary['type'] === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-sky-50 border-sky-200 text-sky-900' }}" role="status">
    <p class="text-sm">{!! $primary['message'] !!}</p>
    @if(isset($primary['action']))
    <a href="{{ $primary['action']['url'] }}" class="text-sm font-semibold underline shrink-0">{{ $primary['action']['label'] }} →</a>
    @endif
</div>
@endif
