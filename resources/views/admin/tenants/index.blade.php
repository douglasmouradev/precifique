<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Tenants" subtitle="Gerencie contas de clientes" />
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap gap-3 items-center justify-between">
            <x-ui.button variant="secondary" :href="route('admin.tenants.create')">+ Criar tenant</x-ui.button>
        </div>

        <form method="GET" class="ui-card p-4 mb-6 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="ui-label text-xs">Buscar</label>
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nome ou e-mail" class="ui-input py-2">
            </div>
            <div class="w-36">
                <label class="ui-label text-xs">Plano</label>
                <select name="plan" class="ui-input py-2">
                    <option value="">Todos</option>
                    <option value="basic" @selected(($filters['plan'] ?? '') === 'basic')>Basic</option>
                    <option value="premium" @selected(($filters['plan'] ?? '') === 'premium')>Premium</option>
                </select>
            </div>
            <div class="w-36">
                <label class="ui-label text-xs">Status</label>
                <select name="status" class="ui-input py-2">
                    <option value="">Todos</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Ativos</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inativos</option>
                    <option value="trial" @selected(($filters['status'] ?? '') === 'trial')>Em trial</option>
                </select>
            </div>
            <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-slate-500 hover:text-brand">Limpar</a>
        </form>

        <x-ui.card class="overflow-x-auto p-0">
            <table class="ui-table">
                <thead>
                    <tr><th>Nome</th><th>E-mail</th><th>Plano</th><th>Trial</th><th>Ativo</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($tenants as $tenant)
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
                @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-500">Nenhum tenant encontrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </x-ui.card>
        <div class="mt-4">{{ $tenants->links() }}</div>
    </div>
</x-app-layout>
