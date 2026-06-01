@extends('layouts.tenant')
@section('title', 'Custos fixos')
@section('breadcrumb') Custos fixos @endsection

@section('content')
<x-ui.page-header title="Custos fixos mensais" :subtitle="'Total ativo: R$ '.number_format($total, 2, ',', '.')">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.variable-costs.index')">Custos variáveis</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid lg:grid-cols-2 gap-6 animate-fade-in">
    <x-ui.card>
        <x-slot:header>
            <h2 class="ui-section-title mb-0">Adicionar custo</h2>
        </x-slot:header>
        <form method="POST" action="{{ route('tenant.fixed-costs.store') }}" class="space-y-4">
            @csrf
            <div><label class="ui-label">Nome</label><input name="name" placeholder="Ex: Aluguel" required class="ui-input"></div>
            <div><label class="ui-label">Valor (R$)</label><input name="amount" type="number" step="0.01" placeholder="0,00" required class="ui-input"></div>
            <div><label class="ui-label">Descrição</label><input name="description" placeholder="Opcional" class="ui-input"></div>
            <x-ui.button type="submit" class="w-full py-2.5">Adicionar custo</x-ui.button>
        </form>
    </x-ui.card>

    <div class="space-y-4">
        @forelse($fixedCosts as $cost)
        <x-ui.card>
            <form method="POST" action="{{ route('tenant.fixed-costs.update', $cost) }}" class="p-5 space-y-3">
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
                :action="route('tenant.fixed-costs.destroy', $cost)"
                :message="'Remover o custo «'.$cost->name.'»?'"
            >Remover {{ $cost->name }}</x-ui.confirm-delete>
        </div>
        @empty
        <x-ui.empty-state
            icon="fixed-costs"
            title="Nenhum custo fixo"
            description="Cadastre aluguel, energia, internet e outros custos mensais do negócio."
        />
        @endforelse
    </div>
</div>
@endsection
