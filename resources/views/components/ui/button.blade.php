@props(['variant' => 'primary', 'href' => null, 'type' => 'button', 'size' => 'md'])
@php
$classes = match($variant) {
    'primary' => 'ui-btn-primary',
    'secondary' => 'ui-btn-secondary',
    'outline' => 'ui-btn-outline',
    'ghost' => 'ui-btn-ghost',
    default => 'ui-btn-primary',
};
$sizeClass = match($size) {
    'lg' => 'px-6 py-3.5 text-base',
    'sm' => 'px-3 py-2 text-xs',
    default => 'px-4 py-2.5',
};
$base = $classes . ' ' . $sizeClass;
@endphp
@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $base]) }}>{{ $slot }}</button>
@endif
