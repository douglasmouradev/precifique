@props(['name', 'label'])

<div {{ $attributes->merge(['class' => 'w-full h-full [&>svg]:w-full [&>svg]:h-full [&>svg]:block']) }} role="img" aria-label="{{ $label }}">
@switch($name)
    @case('dashboard')
        @include('components.landing.previews.dashboard')
        @break
    @case('products')
        @include('components.landing.previews.products')
        @break
    @case('pricing')
        @include('components.landing.previews.pricing')
        @break
@endswitch
</div>
