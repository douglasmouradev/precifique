<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Logs e uso de IA" subtitle="Auditoria e chamadas à API de inteligência artificial" />
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto sm:px-6 space-y-8">
        <div>
            <h3 class="ui-section-title">Últimas chamadas de IA</h3>
            <x-ui.card class="divide-y divide-slate-100 p-0 overflow-hidden">
                @forelse($aiLogs as $log)
                <div class="px-4 py-3 flex justify-between gap-4 text-sm">
                    <span><span class="font-medium">{{ $log->action }}</span> — {{ $log->tenant?->name ?? '—' }}</span>
                    <span class="text-slate-400 shrink-0">{{ $log->created_at->format('d/m H:i') }}</span>
                </div>
                @empty
                <p class="p-4 text-slate-400 text-sm">Nenhum uso de IA registrado.</p>
                @endforelse
            </x-ui.card>
        </div>
        <div>
            <h3 class="ui-section-title">Auditoria geral</h3>
            <x-ui.card class="overflow-x-auto p-0">
                <table class="ui-table">
                    <thead>
                        <tr><th>Data</th><th>Tenant</th><th>Ação</th><th>IP</th></tr>
                    </thead>
                    <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->tenant?->name ?? '—' }}</td>
                        <td>{{ $log->action }}</td>
                        <td class="text-slate-400">{{ $log->ip_address }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </x-ui.card>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>
