@props([
    'variant' => 'full', // full | icon | sidebar
    'size' => 'md',     // sm | md | lg | xl
    'dark' => false,    // texto claro (header/sidebar escuro)
])

@php
    $sizes = [
        'sm' => ['icon' => 'h-9 w-9',  'text' => 'text-lg',   'gap' => 'gap-2.5'],
        'md' => ['icon' => 'h-11 w-11', 'text' => 'text-xl',   'gap' => 'gap-3'],
        'lg' => ['icon' => 'h-12 w-12', 'text' => 'text-2xl',  'gap' => 'gap-3'],
        'xl' => ['icon' => 'h-16 w-16', 'text' => 'text-3xl',  'gap' => 'gap-4'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $textColor = $dark ? 'text-white' : 'text-ink';
@endphp

@if($variant === 'icon')
    <x-ui.logo-icon {{ $attributes->merge(['class' => $s['icon']]) }} />
@elseif($variant === 'sidebar')
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 max-w-full min-w-0']) }} role="img" aria-label="Precifique">
        <x-ui.logo-icon class="h-9 w-9 shrink-0 drop-shadow-[0_2px_10px_rgba(0,200,150,0.4)]" />
        <span class="font-display font-bold text-[1.125rem] leading-none tracking-tight text-white truncate">
            Preci<span class="text-brand">$</span>ique
        </span>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'inline-flex items-center flex-nowrap '.$s['gap']]) }}>
        <x-ui.logo-icon class="{{ $s['icon'] }} shrink-0 drop-shadow-[0_2px_8px_rgba(0,200,150,0.35)]" />
        <span class="font-display font-bold {{ $s['text'] }} leading-none tracking-tight whitespace-nowrap {{ $textColor }} select-none">
            Preci<span class="text-brand">$</span>ique
        </span>
    </div>
@endif
