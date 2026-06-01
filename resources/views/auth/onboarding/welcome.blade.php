@extends('layouts.onboarding')
@section('title', 'Bem-vindo — Precifique')
@section('content')
<div class="text-center">
    <h1 class="font-display text-2xl sm:text-3xl font-bold max-w-lg mx-auto">Você sabe como precificar o que você produz?</h1>
    <p class="text-slate-500 mt-4 max-w-md mx-auto">Em poucos passos configuramos seu painel para o seu tipo de negócio.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center mt-10">
        <x-ui.button variant="secondary" :href="route('onboarding.niche')" class="px-8 py-3.5">Quero aprender!</x-ui.button>
        <x-ui.button variant="outline" :href="route('onboarding.skip')" class="px-8 py-3.5">Já sei precificar</x-ui.button>
    </div>
</div>
@endsection
