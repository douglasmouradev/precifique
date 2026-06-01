@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])
@php
$classes = match($variant) {
    'primary' => 'ui-btn-primary',
    'secondary' => 'ui-btn-secondary',
    'outline' => 'ui-btn-outline',
    'ghost' => 'ui-btn-ghost',
    default => 'ui-btn-primary',
};
$base = $classes . ' px-4 py-2.5';
@endphp
@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</button>
@endif
