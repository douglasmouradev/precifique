@extends('layouts.auth')
@section('title', 'Cadastro — Precifique')
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">Começar grátis</h1>
<p class="text-sm text-slate-500 text-center mb-6">14 dias de trial Premium inclusos</p>

<form method="POST" action="{{ route('tenant.register.store') }}" class="space-y-4">
    @csrf
    <x-ui.input label="Nome do negócio" name="name" value="{{ old('name') }}" required />
    <x-ui.input label="E-mail" name="email" type="email" value="{{ old('email') }}" required />
    <x-ui.input label="Senha" name="password" type="password" required />
    <x-ui.input label="Confirmar senha" name="password_confirmation" type="password" required />
    <x-ui.select label="Nicho" name="niche">
        <option value="alimentos" @selected(old('niche') === 'alimentos')>Alimentos</option>
        <option value="servico" @selected(old('niche') === 'servico')>Serviços</option>
        <option value="artesanato" @selected(old('niche') === 'artesanato')>Artesanato</option>
        <option value="outro" @selected(old('niche') === 'outro')>Outro</option>
    </x-ui.select>
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">Criar conta</x-ui.button>
</form>
@endsection
@section('footer')
<p>Já tem conta? <a href="{{ route('tenant.login') }}" class="text-brand font-semibold hover:underline">Entrar</a></p>
@endsection
