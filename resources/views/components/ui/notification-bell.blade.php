@props(['class' => ''])

<div
    class="relative {{ $class }}"
    x-data="{
        open: false,
        unread: 0,
        items: [],
        loading: false,
        es: null,
        async fetchNotifications() {
            this.loading = true;
            try {
                const r = await fetch('{{ route('tenant.notifications.index') }}', { headers: { 'Accept': 'application/json' } });
                const d = await r.json();
                this.unread = d.unread_count ?? 0;
                this.items = d.notifications ?? [];
            } catch (e) {}
            finally { this.loading = false; }
        },
        connectStream() {
            if (!window.EventSource) return;
            this.es?.close();
            this.es = new EventSource('{{ route('tenant.notifications.stream') }}');
            this.es.onmessage = (e) => {
                try {
                    const d = JSON.parse(e.data);
                    if (d.unread_count !== undefined) this.unread = d.unread_count;
                } catch (err) {}
            };
            this.es.onerror = () => {
                this.es?.close();
                setTimeout(() => this.connectStream(), 5000);
            };
        },
        async markRead(id) {
            await fetch(`{{ url('/app/notifications') }}/${id}/read`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });
            await this.fetchNotifications();
        },
        toggle() {
            this.open = !this.open;
            if (this.open) this.fetchNotifications();
        },
    }"
    x-init="fetchNotifications(); connectStream(); document.addEventListener('visibilitychange', () => { if (document.hidden) { es?.close(); } else { connectStream(); fetchNotifications(); } })"
    @click.outside="open = false"
>
    <button
        type="button"
        @click="toggle()"
        class="relative p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600"
        aria-label="{{ __('notifications.aria') }}"
        :aria-expanded="open"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span x-show="unread > 0" x-cloak class="absolute -top-1 -right-1 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center" x-text="unread > 9 ? '9+' : unread"></span>
    </button>
    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] ui-card shadow-lg z-[70] overflow-hidden"
    >
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <p class="font-semibold text-sm text-ink">{{ __('notifications.title') }}</p>
            <form method="POST" action="{{ route('tenant.notifications.read-all') }}">@csrf
                <button type="submit" class="text-xs text-brand font-semibold hover:underline">{{ __('notifications.mark_all') }}</button>
            </form>
        </div>
        <div class="max-h-72 overflow-y-auto">
            <template x-if="items.length === 0">
                <p class="p-4 text-sm text-slate-500 text-center">{{ __('notifications.empty') }}</p>
            </template>
            <template x-for="item in items" :key="item.id">
                <a
                    :href="item.action_url || '#'"
                    @click="if (!item.read_at) markRead(item.id)"
                    class="block px-4 py-3 border-b border-slate-50 hover:bg-slate-50 text-sm"
                    :class="item.read_at ? 'opacity-70' : 'bg-brand/5'"
                >
                    <p class="font-semibold text-ink" x-text="item.title"></p>
                    <p class="text-slate-500 text-xs mt-0.5 line-clamp-2" x-text="item.body"></p>
                </a>
            </template>
        </div>
    </div>
</div>
