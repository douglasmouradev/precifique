@props([
    'action',
    'title' => null,
    'message' => null,
    'confirmLabel' => null,
])

@php
    $title = $title ?? __('app.confirm_delete.title');
    $message = $message ?? __('app.confirm_delete.message');
    $confirmLabel = $confirmLabel ?? __('app.confirm_delete.confirm');
@endphp

<div x-data="{ open: false }" class="inline">
    <button type="button" @click="open = true" {{ $attributes->merge(['class' => 'text-sm text-red-500 hover:text-red-600 font-medium']) }}>
        {{ $slot->isEmpty() ? __('app.actions.delete') : $slot }}
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition @click.stop class="relative w-full max-w-md ui-card p-6 shadow-2xl">
            <h3 class="font-display text-lg font-bold text-ink">{{ $title }}</h3>
            <p class="text-sm text-slate-600 mt-2">{{ $message }}</p>
            <div class="flex gap-3 mt-6 justify-end">
                <x-ui.button type="button" variant="outline" @click="open = false">{{ __('app.actions.cancel') }}</x-ui.button>
                <form method="POST" action="{{ $action }}">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" class="!bg-red-600 !text-white hover:!bg-red-700">{{ $confirmLabel }}</x-ui.button>
                </form>
            </div>
        </div>
    </div>
</div>
