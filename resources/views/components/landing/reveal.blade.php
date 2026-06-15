@props(['as' => 'div', 'delay' => 0])

<{{ $as }}
    {{ $attributes->merge(['class' => 'scroll-reveal']) }}
    @if($delay) style="transition-delay: {{ (int) $delay }}ms" @endif
>{{ $slot }}</{{ $as }}>
