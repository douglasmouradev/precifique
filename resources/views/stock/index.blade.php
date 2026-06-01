@extends('layouts.tenant')
@section('title', 'Estoque')
@section('breadcrumb') Estoque @endsection

@section('content')
<x-ui.page-header title="Controle de estoque" subtitle="Atualize quantidades e alertas mínimos" />

@if($lowStock->isNotEmpty())
<x-ui.alert type="warning" class="mb-6">
    <p class="font-semibold mb-2">{{ $lowStock->count() }} produto(s) com estoque baixo</p>
    <ul class="space-y-1">
        @foreach($lowStock as $p)
        <li>{{ $p->name }} — {{ $p->stock_quantity }} un. (mín: {{ $p->min_stock_alert }})</li>
        @endforeach
    </ul>
</x-ui.alert>
@endif

<div class="md:hidden space-y-3 mb-6">
    @forelse($products as $product)
    <article class="ui-card p-4 {{ $product->stock_quantity <= $product->min_stock_alert ? 'ring-2 ring-amber-200' : '' }}">
        <p class="font-semibold text-ink">{{ $product->name }}</p>
        <form method="POST" action="{{ route('tenant.stock.update', $product) }}" class="grid grid-cols-2 gap-2 mt-3">@csrf @method('PATCH')
            <div><label class="ui-label text-xs">Estoque</label><input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="ui-input py-1.5"></div>
            <div><label class="ui-label text-xs">Alerta mín.</label><input type="number" name="min_stock_alert" value="{{ $product->min_stock_alert }}" class="ui-input py-1.5"></div>
            <x-ui.button type="submit" class="col-span-2 py-2">Salvar</x-ui.button>
        </form>
    </article>
    @empty
    <x-ui.empty-state icon="stock" title="Sem produtos no estoque" description="Cadastre produtos para controlar quantidades.">
        <x-ui.button :href="route('tenant.products.create')">Novo produto</x-ui.button>
    </x-ui.empty-state>
    @endforelse
</div>

<x-ui.card :padding="false" class="overflow-x-auto hidden md:block">
    <table class="ui-table">
        <thead>
            <tr><th>Produto</th><th>Estoque</th><th>Alerta mín.</th><th class="text-right">Atualizar</th></tr>
        </thead>
        <tbody>
        @forelse($products as $product)
        <tr class="{{ $product->stock_quantity <= $product->min_stock_alert ? 'bg-amber-50/60' : '' }}">
            <td class="font-medium text-ink">{{ $product->name }}</td>
            <td>
                @if($product->stock_quantity <= $product->min_stock_alert)
                <span class="ui-badge bg-amber-100 text-amber-800">{{ $product->stock_quantity }}</span>
                @else
                {{ $product->stock_quantity }}
                @endif
            </td>
            <td class="text-slate-500">{{ $product->min_stock_alert }}</td>
            <td>
                <form method="POST" action="{{ route('tenant.stock.update', $product) }}" class="flex flex-wrap gap-2 items-center justify-end">@csrf @method('PATCH')
                    <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="ui-input w-20 py-1.5" title="Estoque">
                    <input type="number" name="min_stock_alert" value="{{ $product->min_stock_alert }}" class="ui-input w-20 py-1.5" title="Alerta mín.">
                    <x-ui.button type="submit" variant="ghost" class="py-1.5 px-3">OK</x-ui.button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="p-0"><x-ui.empty-state icon="stock" title="Sem produtos" class="border-0 shadow-none"><x-ui.button :href="route('tenant.products.create')">Novo produto</x-ui.button></x-ui.empty-state></td></tr>
        @endforelse
        </tbody>
    </table>
</x-ui.card>
@endsection
