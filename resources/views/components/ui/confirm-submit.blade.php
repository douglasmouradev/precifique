@props([
    'message',
    'title' => null,
    'confirmLabel' => null,
])

@php
    $title = $title ?? __('app.confirm_delete.title');
    $confirmLabel = $confirmLabel ?? __('app.actions.confirm');
@endphp

<div data-confirm-submit class="contents">
    {{ $slot }}

    <dialog data-confirm-submit-dialog class="confirm-delete-dialog" aria-labelledby="confirm-submit-title">
        <div class="ui-card p-6 shadow-2xl w-full">
            <h3 id="confirm-submit-title" class="font-display text-lg font-bold text-ink">{{ $title }}</h3>
            <p class="text-sm text-slate-600 mt-2">{{ $message }}</p>
            <div class="flex gap-3 mt-6 justify-end">
                <button type="button" data-confirm-submit-cancel class="ui-btn-outline px-4 py-2.5">
                    {{ __('app.actions.cancel') }}
                </button>
                <button type="button" data-confirm-submit-confirm class="ui-btn !bg-red-600 !text-white hover:!bg-red-700 px-4 py-2.5">
                    {{ $confirmLabel }}
                </button>
            </div>
        </div>
    </dialog>
</div>
