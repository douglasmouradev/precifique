@extends('layouts.tenant')
@section('title', 'Upgrade Premium')
@section('breadcrumb') Assinatura @endsection

@section('content')
@if($tenant->isPremium())
<div class="max-w-4xl mx-auto py-8">
    <x-ui.page-header title="Assinatura Premium" subtitle="Seu plano está ativo">
        <x-slot:actions>
            @if($tenant->subscription?->stripe_subscription_id)
            <x-ui.button :href="route('tenant.billing.portal')">Gerenciar no Stripe</x-ui.button>
            @endif
            <x-ui.button variant="outline" :href="route('tenant.account.index')">Minha conta</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>
    <x-ui.alert type="success">Você tem acesso a todos os recursos Premium.</x-ui.alert>
</div>
@else
<div class="max-w-4xl mx-auto py-8">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-brand/20 to-amber-100 mb-6 ring-1 ring-brand/20">
            <x-ui.nav-icon name="spark" class="w-8 h-8 text-brand-dark" />
        </div>
        <h1 class="ui-page-title text-balance">Desbloqueie todo o potencial do Precifique</h1>
        <p class="ui-page-subtitle mt-3 max-w-lg mx-auto">
            Produtos ilimitados, IA de precificação, relatórios Excel e margem de até 150%.
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
        <x-ui.card class="border-2 border-slate-200">
            <p class="text-xs font-bold uppercase text-slate-400 tracking-wide">Plano atual</p>
            <h2 class="font-display text-xl font-bold mt-2">Basic</h2>
            <p class="text-3xl font-bold mt-2">Grátis</p>
            <ul class="mt-6 space-y-2 text-sm text-slate-600">
                <li class="flex gap-2"><span class="text-brand">✓</span> Até 5 produtos</li>
                <li class="flex gap-2"><span class="text-brand">✓</span> 3 margens de lucro</li>
            </ul>
        </x-ui.card>

        <x-ui.card class="border-2 border-brand ring-4 ring-brand/10 relative overflow-hidden shadow-premium-glow">
            <span class="absolute top-4 right-4 ui-badge-premium">Recomendado</span>
            <p class="text-xs font-bold uppercase text-brand-dark tracking-wide">Premium</p>
            <h2 class="font-display text-xl font-bold mt-2">Premium</h2>
            <p class="text-3xl font-bold mt-2">R$ {{ number_format($plan?->price_monthly ?? 29.90, 2, ',', '.') }}<span class="text-base font-normal text-slate-500">/mês</span></p>
            <ul class="mt-6 space-y-2 text-sm text-slate-600">
                @foreach($plan?->features ?? ['Produtos ilimitados', 'IA integrada', 'Relatório Excel', '5 margens de lucro'] as $f)
                <li class="flex gap-2"><span class="text-brand">✓</span> {{ is_string($f) ? $f : $f }}</li>
                @endforeach
            </ul>
            <div class="flex flex-col gap-3 mt-8">
                <form method="POST" action="{{ route('tenant.billing.stripe') }}">@csrf
                    <x-ui.button type="submit" variant="secondary" class="w-full py-3">Cartão de crédito</x-ui.button>
                </form>
                <x-ui.button variant="outline" :href="route('tenant.billing.pix')" class="w-full py-3">PIX instantâneo</x-ui.button>
            </div>
        </x-ui.card>
    </div>
</div>
@endif
@endsection
