@props(['label' => null, 'name' => null, 'type' => 'text', 'error' => null])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
    <label @if($name) for="{{ $name }}" @endif class="ui-label">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        @if($name) name="{{ $name }}" id="{{ $name }}" @endif
        {{ $attributes->except('class')->merge(['class' => 'ui-input']) }}
    />
    @if($error)
    <p class="text-red-500 text-sm mt-1">{{ $error }}</p>
    @elseif($name)
    @error($name)<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    @endif
</div>
