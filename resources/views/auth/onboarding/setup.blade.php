@extends('layouts.onboarding', ['step' => 4])
@section('title', 'Configuração inicial — Precifique')
@section('content')
<h1 class="font-display text-xl sm:text-2xl font-bold mb-2">Últimos detalhes</h1>
<p class="text-sm text-slate-500 mb-8">Configure seu negócio e um custo fixo para começar a precificar.</p>

<form method="POST" action="{{ route('onboarding.setup.store') }}" enctype="multipart/form-data" class="ui-card p-6 sm:p-8 space-y-4">
    @csrf
    <x-ui.input label="Nome do negócio" name="name" value="{{ old('name', auth('tenant')->user()->name) }}" required />
    <div>
        <label class="ui-label">Logo (opcional)</label>
        <input type="file" name="logo" accept="image/*" class="ui-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand/10 file:text-brand-dark file:font-semibold">
    </div>
    <x-ui.input label="Custo fixo (ex: Aluguel)" name="fixed_cost_name" value="{{ old('fixed_cost_name') }}" required placeholder="Nome do custo" />
    <x-ui.input label="Valor mensal (R$)" name="fixed_cost_amount" type="number" step="0.01" value="{{ old('fixed_cost_amount') }}" required />
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">Finalizar configuração</x-ui.button>
</form>
@endsection
