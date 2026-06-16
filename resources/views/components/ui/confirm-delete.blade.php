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

<div data-confirm-delete class="inline">
    <button
        type="button"
        data-confirm-delete-trigger
        {{ $attributes->merge(['class' => 'text-sm text-red-500 hover:text-red-600 font-medium touch-manipulation']) }}
    >
        {{ $slot->isEmpty() ? __('app.actions.delete') : $slot }}
    </button>

    <dialog data-confirm-delete-dialog class="confirm-delete-dialog" aria-labelledby="confirm-delete-title">
        <div class="ui-card p-6 shadow-2xl w-full">
            <h3 id="confirm-delete-title" class="font-display text-lg font-bold text-ink">{{ $title }}</h3>
            <p class="text-sm text-slate-600 mt-2">{{ $message }}</p>
            <div class="flex gap-3 mt-6 justify-end">
                <button type="button" data-confirm-delete-cancel class="ui-btn-outline px-4 py-2.5">
                    {{ __('app.actions.cancel') }}
                </button>
                <form method="POST" action="{{ $action }}">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" class="!bg-red-600 !text-white hover:!bg-red-700">{{ $confirmLabel }}</x-ui.button>
                </form>
            </div>
        </div>
    </dialog>
</div>
