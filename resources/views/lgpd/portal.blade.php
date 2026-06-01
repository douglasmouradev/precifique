@extends('layouts.tenant')
@section('title', 'Portal do titular')
@section('content')
<h1 class="font-display text-2xl font-bold mb-6">Portal do titular (LGPD)</h1>
<div class="bg-white rounded-xl p-6 shadow-sm space-y-4 max-w-xl">
    <p class="text-gray-600 text-sm">Gerencie seus dados pessoais conforme a Lei Geral de Proteção de Dados.</p>
    <a href="{{ route('tenant.lgpd.export') }}" class="block border rounded-lg px-4 py-3 hover:border-brand">📥 Exportar meus dados (JSON)</a>
    <form method="POST" action="{{ route('tenant.lgpd.destroy') }}" onsubmit="return confirm('Esta ação anonimiza sua conta. Digite EXCLUIR para confirmar.')">
        @csrf @method('DELETE')
        <label class="text-sm">Digite EXCLUIR para confirmar exclusão:</label>
        <input name="confirm" class="w-full rounded-lg border-gray-300 mt-1 mb-2">
        <button class="text-red-600 font-semibold text-sm">Solicitar exclusão da conta</button>
    </form>
</div>
@endsection
