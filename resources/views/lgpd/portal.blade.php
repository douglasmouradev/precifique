@extends('layouts.tenant')
@section('title', 'Portal do titular')
@section('content')
<x-ui.page-header title="Portal do titular (LGPD)" subtitle="Gerencie seus dados pessoais" />

<div class="max-w-xl space-y-4">
    <x-ui.card>
        <p class="text-sm text-slate-600 mb-4">Exporte uma cópia dos seus dados ou solicite a exclusão da conta.</p>
        <x-ui.button variant="outline" :href="route('tenant.lgpd.export')" class="w-full justify-center">Exportar meus dados (JSON)</x-ui.button>
    </x-ui.card>

    <x-ui.card>
        <h2 class="ui-section-title text-red-700">Excluir conta</h2>
        <form method="POST" action="{{ route('tenant.lgpd.destroy') }}" onsubmit="return confirm('Esta ação anonimiza sua conta permanentemente. Continuar?')" class="space-y-3">
            @csrf @method('DELETE')
            <div>
                <label for="lgpd-confirm" class="ui-label">Digite EXCLUIR para confirmar</label>
                <input id="lgpd-confirm" name="confirm" class="ui-input" autocomplete="off" required>
            </div>
            <div>
                <label for="lgpd-password" class="ui-label">Sua senha atual</label>
                <input id="lgpd-password" type="password" name="password" class="ui-input" required autocomplete="current-password">
                @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <x-ui.button type="submit" variant="outline" class="text-red-600 border-red-200 hover:bg-red-50">Solicitar exclusão</x-ui.button>
        </form>
    </x-ui.card>
</div>
@endsection
