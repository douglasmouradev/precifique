<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Tenants" subtitle="Gerencie contas de clientes" />
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <x-ui.button variant="secondary" :href="route('admin.tenants.create')">+ Criar tenant</x-ui.button>
        </div>
        <x-ui.card class="overflow-x-auto p-0">
            <table class="ui-table">
                <thead>
                    <tr><th>Nome</th><th>E-mail</th><th>Plano</th><th>Trial</th><th>Ativo</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($tenants as $tenant)
                <tr>
                    <td class="font-medium text-ink">
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="hover:text-brand hover:underline">{{ $tenant->name }}</a>
                    </td>
                    <td>{{ $tenant->email }}</td>
                    <td>
                        <span class="ui-badge-brand">{{ $tenant->plan?->value ?? $tenant->plan }}</span>
                    </td>
                    <td class="text-slate-500">
                        @if($tenant->onTrial())
                        até {{ $tenant->trial_ends_at->format('d/m/Y') }}
                        @else
                        —
                        @endif
                    </td>
                    <td>{{ $tenant->is_active ? 'Sim' : 'Não' }}</td>
                    <td>
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-brand text-xs font-semibold hover:underline mr-3">Detalhes</a>
                        <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}" class="inline">@csrf @method('PATCH')
                            <button class="text-slate-600 text-xs font-semibold hover:underline">{{ $tenant->is_active ? 'Desativar' : 'Ativar' }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </x-ui.card>
        <div class="mt-4">{{ $tenants->links() }}</div>
    </div>
</x-app-layout>
