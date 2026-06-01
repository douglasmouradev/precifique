<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Planos" subtitle="Preços e limites exibidos na landing e no app" />
    </x-slot>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 space-y-6">
        @foreach($plans as $plan)
        <x-ui.card class="p-6">
            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-4">
                @csrf @method('PATCH')
                <div class="flex items-center justify-between gap-4">
                    <h3 class="font-display font-bold text-lg capitalize">{{ $plan->slug }}</h3>
                    <span class="ui-badge-brand">{{ $plan->is_active ? 'Ativo' : 'Inativo' }}</span>
                </div>
                <x-ui.input label="Nome exibido" name="name" value="{{ $plan->name }}" />
                <x-ui.input label="Preço mensal (R$)" name="price_monthly" type="number" step="0.01" value="{{ $plan->price_monthly }}" />
                <x-ui.input label="Máx. produtos (vazio = ilimitado)" name="max_products" type="number" value="{{ $plan->max_products }}" />
                <label class="flex gap-2 text-sm text-slate-700 items-center">
                    <input type="checkbox" name="has_ai" value="1" @checked($plan->has_ai) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                    IA incluída
                </label>
                <label class="flex gap-2 text-sm text-slate-700 items-center">
                    <input type="checkbox" name="is_active" value="1" @checked($plan->is_active) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                    Plano ativo na landing
                </label>
                <x-ui.button variant="secondary" type="submit">Salvar plano</x-ui.button>
            </form>
        </x-ui.card>
        @endforeach
    </div>
</x-app-layout>
