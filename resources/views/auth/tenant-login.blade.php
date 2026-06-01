@extends('layouts.auth')
@section('title', 'Entrar — Precifique')
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">Entrar na sua conta</h1>
<p class="text-sm text-slate-500 text-center mb-6">Acesse seu painel de precificação</p>

<form method="POST" action="{{ route('tenant.login.store') }}" class="space-y-4">
    @csrf
    <x-ui.input label="E-mail" name="email" type="email" value="{{ old('email') }}" required autofocus />
    <x-ui.input label="Senha" name="password" type="password" required />
    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand focus:ring-brand/30">
        Lembrar-me
    </label>
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">Entrar</x-ui.button>
</form>
@endsection
@section('footer')
<p><a href="{{ route('tenant.password.request') }}" class="text-brand font-medium hover:underline">Esqueci minha senha</a></p>
<p class="mt-2">Não tem conta? <a href="{{ route('tenant.register') }}" class="text-brand font-semibold hover:underline">Cadastre-se grátis</a></p>
@endsection
