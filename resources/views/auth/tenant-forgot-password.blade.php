@extends('layouts.auth')
@section('title', 'Recuperar senha')
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">Recuperar senha</h1>
<p class="text-sm text-slate-500 text-center mb-6">Enviaremos um link para redefinir sua senha</p>

@if(session('status'))
<x-ui.alert class="ui-alert-success mb-4">{{ session('status') }}</x-ui.alert>
@endif

<form method="POST" action="{{ route('tenant.password.email') }}" class="space-y-4">
    @csrf
    <x-ui.input label="E-mail" name="email" type="email" required autofocus />
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">Enviar link</x-ui.button>
</form>
@endsection
@section('footer')
<a href="{{ route('tenant.login') }}" class="text-brand font-medium hover:underline">Voltar ao login</a>
@endsection
