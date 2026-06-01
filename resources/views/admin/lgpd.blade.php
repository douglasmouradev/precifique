<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="LGPD — Consentimentos" subtitle="Registro de aceites de termos e privacidade" />
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto sm:px-6">
        <x-ui.card class="overflow-x-auto p-0">
            <table class="ui-table">
                <thead>
                    <tr><th>Data</th><th>Tenant</th><th>Tipo</th><th>Versão</th><th>IP</th></tr>
                </thead>
                <tbody>
                @foreach($consents as $c)
                <tr>
                    <td class="text-slate-500">{{ $c->consented_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $c->tenant?->name ?? '—' }}</td>
                    <td>{{ $c->consent_type }}</td>
                    <td>{{ $c->version }}</td>
                    <td class="text-slate-400">{{ $c->ip_address }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </x-ui.card>
        <div class="mt-4">{{ $consents->links() }}</div>
    </div>
</x-app-layout>
