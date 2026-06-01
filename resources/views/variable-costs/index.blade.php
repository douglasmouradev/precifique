@extends('layouts.tenant')
@section('title', 'Custos variáveis')
@section('breadcrumb') Custos variáveis @endsection

@section('content')
<x-ui.page-header title="Custos variáveis mensais" subtitle="Gás, embalagem, energia de produção e outros que variam conforme o volume">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.fixed-costs.index')">Custos fixos</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<p class="text-sm text-slate-500 mb-6 -mt-4">Total ativo: <strong class="text-brand-dark">R$ {{ number_format($total, 2, ',', '.') }}</strong> — rateado entre os produtos ativos na precificação.</p>

<div class="grid lg:grid-cols-2 gap-6 animate-fade-in">
    <x-ui.card>
        <x-slot:header>
            <h2 class="ui-section-title mb-0">Adicionar custo variável</h2>
        </x-slot:header>
        <form method="POST" action="{{ route('tenant.variable-costs.store') }}" class="space-y-4">
            @csrf
            <div><label class="ui-label">Nome</label><input name="name" placeholder="Ex: Gás de cozinha" required class="ui-input"></div>
            <div><label class="ui-label">Valor mensal (R$)</label><input name="amount" type="number" step="0.01" placeholder="0,00" required class="ui-input"></div>
            <div><label class="ui-label">Descrição</label><input name="description" placeholder="Opcional" class="ui-input"></div>
            <x-ui.button type="submit" class="w-full py-2.5">Adicionar custo</x-ui.button>
        </form>
    </x-ui.card>

    <div class="space-y-4">
        @forelse($variableCosts as $cost)
        <x-ui.card>
            <form method="POST" action="{{ route('tenant.variable-costs.update', $cost) }}" class="p-5 space-y-3">
                @csrf @method('PUT')
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1"><label class="ui-label">Nome</label><input name="name" value="{{ $cost->name }}" class="ui-input"></div>
                    <div class="sm:w-32"><label class="ui-label">Valor</label><input name="amount" type="number" step="0.01" value="{{ $cost->amount }}" class="ui-input"></div>
                </div>
                <div><label class="ui-label">Descrição</label><input name="description" value="{{ $cost->description }}" class="ui-input"></div>
                <div class="flex flex-wrap justify-between items-center gap-3 pt-1">
                    <label class="text-sm flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="is_active" value="1" @checked($cost->is_active) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                        Ativo
                    </label>
                    <x-ui.button type="submit" variant="outline" class="py-2 px-4">Salvar</x-ui.button>
                </div>
            </form>
        </x-ui.card>
        <div class="text-right -mt-2 px-1">
            <x-ui.confirm-delete
                :action="route('tenant.variable-costs.destroy', $cost)"
                :message="'Remover «'.$cost->name.'»?'"
            >Remover {{ $cost->name }}</x-ui.confirm-delete>
        </div>
        @empty
        <x-ui.empty-state
            icon="variable-costs"
            title="Nenhum custo variável"
            description="Gás, embalagens e energia de produção são rateados entre seus produtos."
        />
        @endforelse
    </div>
</div>
@endsection
