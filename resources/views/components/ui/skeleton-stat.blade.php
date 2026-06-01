@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'ui-card p-5 animate-pulse '.$class]) }}>
    <div class="h-3 w-24 bg-slate-200 rounded mb-4"></div>
    <div class="h-8 w-32 bg-slate-200 rounded"></div>
</div>
