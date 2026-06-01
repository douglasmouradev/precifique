@props([
    'name' => 'modal',
    'title' => '',
    'maxWidth' => 'md',
])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'max-w-sm',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    default => 'max-w-md',
};
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}' || $event.detail === '*') open = false"
    x-on:keydown.escape.window="open = false"
    {{ $attributes }}
>
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[80] flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="open = false"></div>
        <div
            x-show="open"
            x-transition
            @click.stop
            class="relative w-full {{ $maxWidthClass }} ui-card p-6 shadow-2xl"
        >
            @if($title)
            <h3 class="font-display text-lg font-bold text-ink mb-4">{{ $title }}</h3>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
