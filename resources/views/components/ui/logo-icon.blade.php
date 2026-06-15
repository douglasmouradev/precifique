@props([
    'class' => 'h-11 w-11',
])

<img
    {{ $attributes->merge(['class' => $class]) }}
    src="{{ asset('images/logo-icon.svg') }}"
    width="48"
    height="48"
    alt=""
    aria-hidden="true"
    decoding="async"
/>
