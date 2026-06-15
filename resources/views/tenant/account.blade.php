@extends('layouts.tenant')
@section('title', 'Minha conta')
@section('breadcrumb') Conta @endsection

@section('content')
<x-ui.page-header title="Minha conta" subtitle="Dados do negócio, senha e assinatura" />

<div class="grid lg:grid-cols-2 gap-6 max-w-5xl">
    <x-ui.card>
        <h2 class="ui-section-title">Perfil do negócio</h2>
        <form method="POST" action="{{ route('tenant.account.profile') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="name" class="ui-label">Nome</label>
                <input id="name" name="name" value="{{ old('name', $tenant->name) }}" required class="ui-input">
            </div>
            <div>
                <label for="email" class="ui-label">E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email', $tenant->email) }}" required class="ui-input">
            </div>
            <div>
                <label for="niche" class="ui-label">Nicho</label>
                <select id="niche" name="niche" class="ui-input">
                    @foreach(['alimentos' => 'Alimentos', 'servico' => 'Serviços', 'artesanato' => 'Artesanato', 'outro' => 'Outro'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('niche', $tenant->niche?->value ?? (string) $tenant->niche) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <x-ui.button type="submit">Salvar perfil</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card>
        <h2 class="ui-section-title">Alterar senha</h2>
        <form method="POST" action="{{ route('tenant.account.password') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="current_password" class="ui-label">Senha atual</label>
                <input id="current_password" type="password" name="current_password" required class="ui-input" autocomplete="current-password">
                @error('current_password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="ui-label">Nova senha</label>
                <input id="password" type="password" name="password" required class="ui-input" autocomplete="new-password">
            </div>
            <div>
                <label for="password_confirmation" class="ui-label">Confirmar nova senha</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="ui-input" autocomplete="new-password">
            </div>
            <x-ui.button type="submit" variant="outline">Atualizar senha</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card class="lg:col-span-2">
        <h2 class="ui-section-title">Plano e assinatura</h2>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if($tenant->isPremium())
            <span class="ui-badge-premium">Premium ativo</span>
            @elseif($tenant->onTrial())
            <span class="ui-badge-brand">Trial Premium até {{ $tenant->trial_ends_at->format('d/m/Y') }}</span>
            @else
            <span class="text-sm text-slate-600">Plano Basic</span>
            @endif
        </div>
        @if($subscription?->ends_at)
        <p class="text-sm text-slate-600 mb-4">Válido até {{ $subscription->ends_at->format('d/m/Y') }}.</p>
        @endif
        <div class="flex flex-wrap gap-3">
            @if($tenant->isPremium() && $subscription?->stripe_subscription_id)
            <x-ui.button :href="route('tenant.billing.portal')">Gerenciar assinatura</x-ui.button>
            @elseif(!$tenant->isPremium())
            <x-ui.button :href="route('tenant.billing.upgrade')">Fazer upgrade</x-ui.button>
            @endif
            <x-ui.button variant="outline" :href="route('tenant.lgpd.portal')">Privacidade (LGPD)</x-ui.button>
        </div>
        <p class="text-xs text-slate-500 mt-4">Integração API disponível — consulte a documentação no repositório do projeto.</p>
    </x-ui.card>
</div>
@endsection
