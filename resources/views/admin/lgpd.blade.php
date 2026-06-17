<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="__('admin.lgpd_page.title')" :subtitle="__('admin.lgpd_page.subtitle')" />
    </x-slot>
    <div class="space-y-8">
        <x-ui.card class="overflow-x-auto p-0">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.lgpd_page.date') }}</th>
                        <th>{{ __('admin.lgpd_page.tenant') }}</th>
                        <th>{{ __('admin.lgpd_page.type') }}</th>
                        <th>{{ __('admin.lgpd_page.version') }}</th>
                        <th>{{ __('admin.lgpd_page.ip') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($consents as $c)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="text-slate-500 tabular-nums">{{ $c->consented_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $c->tenant?->name ?? '—' }}</td>
                    <td>{{ $c->consent_type }}</td>
                    <td>{{ $c->version }}</td>
                    <td class="text-slate-400">{{ $c->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-0"><x-ui.empty-state icon="dashboard" :title="__('admin.lgpd_page.empty')" class="border-0 shadow-none" /></td></tr>
                @endforelse
                </tbody>
            </table>
        </x-ui.card>
        <div class="mt-4">{{ $consents->links() }}</div>
    </div>
</x-app-layout>
