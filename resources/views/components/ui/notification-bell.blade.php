@props(['class' => ''])

<div
    class="relative {{ $class }}"
    data-notification-bell
    data-index-url="{{ route('tenant.notifications.index') }}"
    data-stream-url="{{ route('tenant.notifications.stream') }}"
    data-read-all-url="{{ route('tenant.notifications.read-all') }}"
    data-read-url-template="{{ url('/app/notifications/__ID__/read') }}"
    data-csrf="{{ csrf_token() }}"
    data-empty="{{ __('notifications.empty') }}"
>
    <button
        type="button"
        data-notification-toggle
        class="relative p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 touch-manipulation"
        aria-label="{{ __('notifications.aria') }}"
        aria-expanded="false"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span data-notification-badge class="hidden absolute -top-1 -right-1 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">0</span>
    </button>
    <div
        data-notification-panel
        class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] ui-card shadow-lg z-[70] overflow-hidden"
    >
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-3">
            <p class="font-semibold text-sm text-ink">{{ __('notifications.title') }}</p>
            <button
                type="button"
                data-notification-mark-all
                class="text-xs text-brand font-semibold hover:underline touch-manipulation shrink-0"
            >{{ __('notifications.mark_all') }}</button>
        </div>
        <div data-notification-list class="max-h-72 overflow-y-auto"></div>
    </div>
</div>
