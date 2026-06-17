@props(['label', 'value', 'icon' => 'dashboard', 'trend' => null, 'accent' => 'brand'])
@php
$accents = [
    'brand' => 'bg-brand/10 text-brand-dark ring-brand/20',
    'blue' => 'bg-slate-100 text-slate-600 ring-slate-200/80',
    'amber' => 'bg-amber-50 text-amber-700 ring-amber-200/60',
    'violet' => 'bg-violet-50 text-violet-700 ring-violet-200/60',
];
@endphp
<div {{ $attributes->merge(['class' => 'ui-stat']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</p>
            <p class="text-2xl font-display font-bold text-ink mt-2 tracking-tight truncate tabular-nums">{{ $value }}</p>
            @if($trend)
            <p class="text-xs text-slate-500 mt-2">{{ $trend }}</p>
            @endif
        </div>
        <div class="ui-stat-icon ring-1 {{ $accents[$accent] ?? $accents['brand'] }}">
            <x-ui.nav-icon :name="$icon" class="w-5 h-5" />
        </div>
    </div>
</div>
