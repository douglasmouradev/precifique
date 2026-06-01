@extends('layouts.tenant')
@section('title', 'Meta do mês')
@section('breadcrumb') Meta @endsection

@section('content')
<x-ui.page-header title="Meta de faturamento" subtitle="Acompanhe seu progresso no dashboard" />

<x-ui.card class="max-w-md">
    <form method="POST" action="{{ route('tenant.goals.store') }}" class="space-y-5">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="ui-label">Ano</label><input type="number" name="year" value="{{ $goal?->year ?? now()->year }}" class="ui-input"></div>
            <div><label class="ui-label">Mês</label><input type="number" name="month" min="1" max="12" value="{{ $goal?->month ?? now()->month }}" class="ui-input"></div>
        </div>
        <div><label class="ui-label">Meta (R$)</label><input type="number" name="goal_amount" step="0.01" value="{{ $goal?->goal_amount }}" required placeholder="Ex: 5000" class="ui-input"></div>
        <x-ui.button type="submit" class="w-full py-3">Salvar meta</x-ui.button>
    </form>
</x-ui.card>
@endsection
