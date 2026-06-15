@extends('layouts.tenant')
@section('title', 'Vendas')
@section('breadcrumb') Vendas @endsection

@section('content')
<x-ui.page-header title="Vendas" subtitle="Histórico, filtros e exportação profissional">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.sales.export', request()->query())">Exportar CSV</x-ui.button>
        <x-ui.button :href="route('tenant.sales.create')">+ Nova venda</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid sm:grid-cols-3 gap-4 mb-6">
    <x-ui.stat label="Receita no período" icon="revenue" accent="brand"
        :value="'R$ '.number_format($totalRevenue, 2, ',', '.')" />
    <x-ui.stat label="Vendas" icon="sales" accent="violet" :value="(string) $salesCount" />
    <x-ui.card class="p-5 flex flex-col justify-center">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">Por pagamento</p>
        <div class="flex flex-wrap gap-2 text-xs">
            @foreach(\App\Enums\PaymentMethod::cases() as $method)
            @php $row = $paymentBreakdown->get($method->value); @endphp
            <span class="ui-badge-brand">{{ $method->label() }}: {{ $row?->count ?? 0 }}</span>
            @endforeach
        </div>
    </x-ui.card>
</div>

<form method="GET" class="ui-card-premium p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div class="min-w-[140px]">
        <label class="ui-label text-xs">Pagamento</label>
        <select name="payment_method" class="ui-input py-2">
            <option value="">Todos</option>
            @foreach(\App\Enums\PaymentMethod::cases() as $method)
            <option value="{{ $method->value }}" @selected(($filters['payment_method'] ?? '') === $method->value)>{{ $method->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="w-28">
        <label class="ui-label text-xs">Mês</label>
        <select name="month" class="ui-input py-2">
            <option value="">Todos</option>
            @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" @selected((int)($filters['month'] ?? now()->month) === $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endfor
        </select>
    </div>
    <div class="w-24">
        <label class="ui-label text-xs">Ano</label>
        <input type="number" name="year" value="{{ $filters['year'] ?? now()->year }}" class="ui-input py-2" min="2020" max="2100">
    </div>
    <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
    <a href="{{ route('tenant.sales.index') }}" class="ui-btn-ghost py-2 text-sm">Limpar</a>
</form>

{{-- Mobile: cards --}}
<div class="md:hidden space-y-3 mb-6">
    @forelse($sales as $sale)
    <article class="ui-card-hover p-4">
        <div class="flex justify-between items-start gap-2">
            <div>
                <p class="font-semibold text-ink">{{ $sale->product?->name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $sale->sold_at->format('d/m/Y H:i') }}</p>
            </div>
            <p class="font-bold text-brand-dark shrink-0">R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</p>
        </div>
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 text-sm">
            <span class="text-slate-500">Qtd: {{ $sale->quantity }} · {{ \App\Enums\PaymentMethod::tryLabel($sale->payment_method) }}</span>
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.sales.edit', $sale) }}" class="text-brand text-xs font-semibold hover:underline">Editar</a>
                <x-ui.confirm-delete :action="route('tenant.sales.destroy', $sale)" message="Excluir esta venda?" />
            </div>
        </div>
    </article>
    @empty
    <x-ui.empty-state icon="sales" title="Nenhuma venda no período" description="Ajuste os filtros ou registre uma nova venda.">
        <x-ui.button :href="route('tenant.sales.create')">Nova venda</x-ui.button>
    </x-ui.empty-state>
    @endforelse
</div>

<x-ui.card :padding="false" class="overflow-hidden hidden md:block shadow-premium-glow">
    <div class="overflow-x-auto">
        <table class="ui-table">
            <thead><tr><th>Data</th><th>Produto</th><th>Qtd</th><th>Total</th><th>Pagamento</th><th></th></tr></thead>
            <tbody>
            @forelse($sales as $sale)
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="text-slate-500">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                <td class="font-medium">{{ $sale->product?->name }}</td>
                <td>{{ $sale->quantity }}</td>
                <td class="font-semibold text-brand-dark">R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                <td><span class="ui-badge-brand">{{ \App\Enums\PaymentMethod::tryLabel($sale->payment_method) }}</span></td>
                <td>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('tenant.sales.edit', $sale) }}" class="text-brand text-xs font-semibold hover:underline">Editar</a>
                        <x-ui.confirm-delete :action="route('tenant.sales.destroy', $sale)" message="Excluir esta venda?" />
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-0"><x-ui.empty-state icon="sales" title="Nenhuma venda no período" class="border-0 shadow-none"><x-ui.button :href="route('tenant.sales.create')">Nova venda</x-ui.button></x-ui.empty-state></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-ui.card>
<div class="mt-6">{{ $sales->links() }}</div>
@endsection
