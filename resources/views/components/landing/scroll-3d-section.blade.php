@props(['as' => 'section', 'intensity' => 'medium'])

<{{ $as }}
    {{ $attributes->merge(['class' => 'scroll-3d-section']) }}
    data-scroll-3d-section
    data-intensity="{{ $intensity }}"
>
    <div class="scroll-3d-section__inner">
        {{ $slot }}
    </div>
</{{ $as }}>
