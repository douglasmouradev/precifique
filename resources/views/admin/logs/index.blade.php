<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="__('admin.logs.title')" :subtitle="__('admin.logs_page.subtitle')" />
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto sm:px-6 space-y-8 animate-fade-in">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[12rem] flex-1">
                <label class="ui-label">{{ __('admin.logs_page.filter_search') }}</label>
                <input type="search" name="q" value="{{ $search ?? '' }}" class="ui-input" placeholder="{{ __('admin.logs_page.filter_search_placeholder') }}">
            </div>
            <div class="min-w-[10rem]">
                <label class="ui-label">{{ __('admin.logs_page.action') }}</label>
                <input type="text" name="action" value="{{ $action ?? '' }}" class="ui-input" placeholder="ai.*">
            </div>
            <x-ui.button type="submit" variant="secondary">{{ __('admin.logs_page.filter_submit') }}</x-ui.button>
        </form>
        <div>
            <h3 class="ui-section-title">{{ __('admin.logs_page.ai_title') }}</h3>
            <x-ui.card class="divide-y divide-slate-100 p-0 overflow-hidden">
                @forelse($aiLogs as $log)
                <div class="px-4 py-3 flex justify-between gap-4 text-sm hover:bg-slate-50/50 transition-colors">
                    <span><span class="font-medium">{{ $log->action }}</span> — {{ $log->tenant?->name ?? '—' }}</span>
                    <span class="text-slate-400 shrink-0 tabular-nums">{{ $log->created_at->format('d/m H:i') }}</span>
                </div>
                @empty
                <x-ui.empty-state icon="spark" :title="__('admin.logs_page.empty_ai')" class="border-0 shadow-none" />
                @endforelse
            </x-ui.card>
        </div>
        <div>
            <h3 class="ui-section-title">{{ __('admin.logs_page.audit_title') }}</h3>
            <x-ui.card class="overflow-x-auto p-0">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.logs_page.date') }}</th>
                            <th>{{ __('admin.logs_page.tenant') }}</th>
                            <th>{{ __('admin.logs_page.action') }}</th>
                            <th>{{ __('admin.logs_page.ip') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="text-slate-500 tabular-nums">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->tenant?->name ?? '—' }}</td>
                        <td>{{ $log->action }}</td>
                        <td class="text-slate-400">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-0"><x-ui.empty-state icon="dashboard" :title="__('admin.logs_page.empty_audit')" class="border-0 shadow-none" /></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </x-ui.card>
            <div class="mt-6">{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>
