@extends('layouts.auth')
@section('title', 'Nova senha')
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-6">Definir nova senha</h1>

<form method="POST" action="{{ route('tenant.password.store') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <x-ui.input label="E-mail" name="email" type="email" value="{{ old('email', $email) }}" required />
    <x-ui.input label="Nova senha" name="password" type="password" required />
    <x-ui.input label="Confirmar senha" name="password_confirmation" type="password" required />
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">Redefinir senha</x-ui.button>
</form>
@endsection
