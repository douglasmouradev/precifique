@extends('layouts.onboarding', ['step' => 2])
@section('title', 'Modo de uso — Precifique')
@section('content')
<h1 class="font-display text-xl sm:text-2xl font-bold text-center mb-2">Como prefere usar o Precifique?</h1>
<p class="text-sm text-slate-500 text-center mb-8">Você pode mudar isso depois nas configurações.</p>

<form method="POST" action="{{ route('onboarding.mode.store') }}" class="grid sm:grid-cols-2 gap-4">
    @csrf
    <label class="p-6 ui-card border-2 border-transparent cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-colors">
        <input type="radio" name="usage_mode" value="iniciante" class="sr-only" required @checked(old('usage_mode', 'iniciante') === 'iniciante')>
        <div class="w-10 h-10 rounded-lg bg-brand/10 text-brand flex items-center justify-center mb-3">
            <x-ui.nav-icon name="spark" class="w-5 h-5" />
        </div>
        <p class="font-bold">Modo Iniciante</p>
        <p class="text-sm text-slate-500 mt-2">Interface simplificada com dicas em cada etapa.</p>
    </label>
    <label class="p-6 ui-card border-2 border-transparent cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-colors">
        <input type="radio" name="usage_mode" value="avancado" class="sr-only" @checked(old('usage_mode') === 'avancado')>
        <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center mb-3">
            <x-ui.nav-icon name="reports" class="w-5 h-5" />
        </div>
        <p class="font-bold">Modo Avançado</p>
        <p class="text-sm text-slate-500 mt-2">Todas as funcionalidades e campos detalhados.</p>
    </label>
    <x-ui.button variant="secondary" type="submit" class="sm:col-span-2 py-3">Continuar</x-ui.button>
</form>
@endsection
