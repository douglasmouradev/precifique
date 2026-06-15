<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header :title="$tenant->name" subtitle="Detalhes da conta" />
    </x-slot>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 space-y-6">
        <x-ui.button variant="ghost" :href="route('admin.tenants.index')">← Voltar</x-ui.button>

        <x-ui.card class="p-6 space-y-4">
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">E-mail</span><p class="font-medium">{{ $tenant->email }}</p></div>
                <div><span class="text-slate-500">Plano</span><p class="font-medium capitalize">{{ $tenant->plan?->value ?? $tenant->plan }}</p></div>
                <div><span class="text-slate-500">Nicho</span><p class="font-medium capitalize">{{ $tenant->niche?->value ?? $tenant->niche ?? '—' }}</p></div>
                <div><span class="text-slate-500">Status</span><p class="font-medium">{{ $tenant->is_active ? 'Ativo' : 'Inativo' }}</p></div>
                <div><span class="text-slate-500">Trial até</span><p class="font-medium">{{ $tenant->trial_ends_at?->format('d/m/Y H:i') ?? '—' }}</p></div>
                <div><span class="text-slate-500">Cadastro</span><p class="font-medium">{{ $tenant->created_at?->format('d/m/Y') }}</p></div>
            </div>
        </x-ui.card>

        @if($tenant->subscription)
        <x-ui.card class="p-6">
            <h3 class="font-display font-bold text-lg mb-3">Assinatura</h3>
            <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                <div><dt class="text-slate-500">Plano</dt><dd class="font-medium">{{ $tenant->subscription->plan?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Status</dt><dd class="font-medium capitalize">{{ $tenant->subscription->status }}</dd></div>
                <div><dt class="text-slate-500">Início</dt><dd class="font-medium">{{ $tenant->subscription->starts_at?->format('d/m/Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Expira</dt><dd class="font-medium">{{ $tenant->subscription->ends_at?->format('d/m/Y') ?? 'Recorrente' }}</dd></div>
            </dl>
        </x-ui.card>
        @endif

        <x-ui.card class="p-6">
            <h3 class="font-display font-bold text-lg mb-3">LGPD recente</h3>
            @forelse($tenant->lgpdConsents as $consent)
            <p class="text-sm text-slate-600">{{ $consent->consent_type }} — {{ $consent->consented_at?->format('d/m/Y H:i') }} (v{{ $consent->version }})</p>
            @empty
            <p class="text-sm text-slate-500">Nenhum consentimento registrado.</p>
            @endforelse
        </x-ui.card>

        <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}">
            @csrf @method('PATCH')
            <x-ui.button variant="secondary" type="submit">{{ $tenant->is_active ? 'Desativar conta' : 'Reativar conta' }}</x-ui.button>
        </form>
    </div>
</x-app-layout>
