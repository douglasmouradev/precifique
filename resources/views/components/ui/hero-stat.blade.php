@props(['label', 'value', 'trend' => null, 'href' => null])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'ui-card-premium p-6 md:p-8 block group']) }}
>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="font-display text-4xl md:text-5xl font-bold text-ink mt-3 tracking-tight tabular-nums">{{ $value }}</p>
    @if($trend)
    <p class="text-sm text-slate-500 mt-3">{{ $trend }}</p>
    @endif
    @if($href)
    <p class="text-xs font-semibold text-brand-dark mt-4 opacity-0 group-hover:opacity-100 transition-opacity">{{ __('dashboard.view_details') }} →</p>
    @endif
</{{ $tag }}>
