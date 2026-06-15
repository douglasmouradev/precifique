@extends('layouts.tenant')
@section('title', 'Portal do titular')
@section('content')
<h1 class="font-display text-2xl font-bold mb-6">Portal do titular (LGPD)</h1>
<div class="bg-white rounded-xl p-6 shadow-sm space-y-4 max-w-xl">
    <p class="text-gray-600 text-sm">Gerencie seus dados pessoais conforme a Lei Geral de Proteção de Dados.</p>
    <a href="{{ route('tenant.lgpd.export') }}" class="block border rounded-lg px-4 py-3 hover:border-brand">📥 Exportar meus dados (JSON)</a>
    <form method="POST" action="{{ route('tenant.lgpd.destroy') }}" onsubmit="return confirm('Esta ação anonimiza sua conta permanentemente. Continuar?')">
        @csrf @method('DELETE')
        <label for="lgpd-confirm" class="text-sm">Digite EXCLUIR para confirmar exclusão:</label>
        <input id="lgpd-confirm" name="confirm" class="w-full rounded-lg border-gray-300 mt-1 mb-2" autocomplete="off">
        <label for="lgpd-password" class="text-sm">Sua senha atual:</label>
        <input id="lgpd-password" type="password" name="password" class="w-full rounded-lg border-gray-300 mt-1 mb-2" required autocomplete="current-password">
        @error('password')<p class="text-red-600 text-sm mb-2">{{ $message }}</p>@enderror
        <button class="text-red-600 font-semibold text-sm">Solicitar exclusão da conta</button>
    </form>
</div>
@endsection
