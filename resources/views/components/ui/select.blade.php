@props(['label' => null, 'name' => null])

<div>
    @if($label)
    <label @if($name) for="{{ $name }}" @endif class="ui-label">{{ $label }}</label>
    @endif
    <select
        @if($name) name="{{ $name }}" id="{{ $name }}" @endif
        {{ $attributes->merge(['class' => 'ui-input']) }}
    >{{ $slot }}</select>
    @if($name)
    @error($name)<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    @endif
</div>
