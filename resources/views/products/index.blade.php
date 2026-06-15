@extends('layouts.tenant')
@section('title', 'Produtos')
@section('breadcrumb') Produtos @endsection

@section('content')
<x-ui.page-header title="Produtos" :subtitle="request()->boolean('unpriced') ? 'Somente produtos sem preço' : 'Gerencie seu catálogo e precifique com precisão'">
    <x-slot:actions>
        <x-ui.button :href="route('tenant.products.create')">+ Novo produto</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

@if($maxProducts)
@php $atLimit = $productCount >= $maxProducts; @endphp
<div class="mb-6 ui-card-premium p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 {{ $atLimit ? 'ring-2 ring-amber-400/30' : '' }}">
    <div>
        <p class="text-sm font-semibold text-ink">Plano Basic — catálogo</p>
        <p class="text-sm text-slate-500 mt-0.5">{{ $productCount }} de {{ $maxProducts }} produtos utilizados</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex-1 sm:w-40 h-2 bg-slate-100 rounded-full overflow-hidden min-w-[120px]">
            <div class="h-full rounded-full transition-all {{ $atLimit ? 'bg-amber-500' : 'bg-brand' }}" style="width: {{ min(100, ($productCount / $maxProducts) * 100) }}%"></div>
        </div>
        @if($atLimit)
        <x-ui.button variant="secondary" :href="route('tenant.billing.upgrade')">Fazer upgrade</x-ui.button>
        @endif
    </div>
</div>
@endif

<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($products as $product)
    <article class="ui-card-hover overflow-hidden group">
        @if($product->photo_path)
        <div class="aspect-[16/10] overflow-hidden bg-slate-100">
            <img src="{{ asset('storage/'.$product->photo_path) }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>
        @else
        <div class="aspect-[16/10] bg-gradient-to-br from-slate-100 to-brand/10 flex items-center justify-center">
            <x-ui.nav-icon name="products" class="w-12 h-12 text-brand/40" />
        </div>
        @endif
        <div class="p-5">
            <h3 class="font-display font-semibold text-lg truncate">{{ $product->name }}</h3>
            <p class="text-2xl font-bold text-brand-dark mt-1">
                {{ $product->selling_price ? 'R$ '.number_format($product->selling_price, 2, ',', '.') : 'Sem preço' }}
            </p>
            @if(!$product->selling_price)
            <span class="ui-badge-brand mt-2">Precificar</span>
            @endif
            <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100 items-center">
                <a href="{{ route('tenant.products.edit', $product) }}" class="text-sm font-semibold text-slate-700 hover:text-brand-dark hover:underline">Editar</a>
                <a href="{{ route('tenant.pricing.edit', $product) }}" class="text-sm font-semibold text-brand-dark hover:underline">Precificar</a>
                <form method="POST" action="{{ route('tenant.products.duplicate', $product) }}" class="inline">@csrf
                    <button type="submit" class="text-sm text-slate-600 hover:text-brand-dark font-medium">Duplicar</button>
                </form>
                @if($product->selling_price)
                <a href="{{ route('tenant.quotes.pdf', $product) }}" class="text-sm text-slate-600 hover:text-brand-dark font-medium">PDF</a>
                @endif
                <x-ui.confirm-delete
                    :action="route('tenant.products.destroy', $product)"
                    message="O produto «{{ $product->name }}» será removido permanentemente."
                />
            </div>
        </div>
    </article>
    @empty
    <div class="sm:col-span-2 xl:col-span-3">
        <x-ui.empty-state
            icon="products"
            title="Nenhum produto cadastrado"
            description="Cadastre seu primeiro produto e use o assistente de precificação para definir o preço ideal."
        >
            <x-ui.button :href="route('tenant.products.create')">Cadastrar primeiro produto</x-ui.button>
        </x-ui.empty-state>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
