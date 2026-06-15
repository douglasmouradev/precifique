@props(['as' => 'div', 'delay' => 0])

<{{ $as }}
    x-data="{ shown: true }"
    x-intersect.once.threshold.10="shown = true"
    {{ $attributes->merge(['class' => 'scroll-reveal is-visible']) }}
    :class="{ 'is-visible': shown }"
    @if($delay) style="transition-delay: {{ (int) $delay }}ms" @endif
>{{ $slot }}</{{ $as }}>
