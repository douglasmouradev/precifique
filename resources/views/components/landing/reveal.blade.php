@props(['as' => 'div', 'delay' => 0])

<{{ $as }}
    x-data="{ shown: false }"
    x-intersect.once.threshold.10.margin.-40px="shown = true"
    {{ $attributes->merge(['class' => 'scroll-reveal']) }}
    :class="{ 'is-visible': shown }"
    @if($delay) style="transition-delay: {{ (int) $delay }}ms" @endif
>{{ $slot }}</{{ $as }}>
