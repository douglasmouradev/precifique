@extends('layouts.tenant')
@section('title', __('products.edit.title'))
@section('breadcrumb') {{ __('products.breadcrumb_edit', ['name' => $product->name]) }} @endsection

@section('content')
<x-ui.page-header :title="__('products.edit.title')" :subtitle="$product->name">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.pricing.edit', $product)">{{ __('products.price_product') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid lg:grid-cols-3 gap-6 max-w-5xl">
    <x-ui.card class="lg:col-span-2">
        <form method="POST" action="{{ route('tenant.products.update', $product) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <div><label class="ui-label">{{ __('products.edit.name') }}</label><input name="name" value="{{ old('name', $product->name) }}" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('products.edit.description') }}</label><textarea name="description" rows="3" class="ui-input">{{ old('description', $product->description) }}</textarea></div>
            <div>
                <label class="ui-label">{{ __('products.edit.niche') }}</label>
                <select name="niche_type" class="ui-input">
                    @foreach(['alimentos', 'servico', 'artesanato'] as $value)
                    <option value="{{ $value }}" @selected(old('niche_type', $product->niche_type?->value ?? $product->niche_type) === $value)>{{ __('app.niches.'.$value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="ui-label">{{ __('products.edit.stock') }}</label><input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}" class="ui-input"></div>
                <div><label class="ui-label">{{ __('products.edit.min_stock_alert') }}</label><input type="number" name="min_stock_alert" min="0" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" class="ui-input"></div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded border-slate-300 text-brand">
                {{ __('products.edit.is_active') }}
            </label>
            <div>
                <label class="ui-label">{{ __('products.edit.photo') }} @if(($tenant->interface_mode ?? '') === 'artesanato' && ! $product->photo_path)<span class="text-red-500">*</span>@endif</label>
                @if($product->photo_path)
                <div class="mb-3 flex items-start gap-4">
                    <img src="{{ asset('storage/'.$product->photo_path) }}" alt="" class="w-24 h-24 object-cover rounded-xl ring-1 ring-slate-200">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remove_photo" value="1" class="rounded border-slate-300">
                        {{ __('products.edit.remove_photo') }}
                    </label>
                </div>
                @endif
                <input type="file" name="photo" accept="image/*" class="ui-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand/10 file:text-brand-dark file:font-semibold">
            </div>
            <div class="flex gap-3 pt-2">
                <x-ui.button type="submit">{{ __('products.edit.save') }}</x-ui.button>
                <x-ui.button variant="ghost" :href="route('tenant.products.index')">{{ __('products.edit.cancel') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <div class="space-y-6">
        <x-ui.card class="p-5">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2">{{ __('products.edit.current_price') }}</p>
            <p class="text-2xl font-bold text-brand-dark">
                {{ $product->selling_price ? 'R$ '.number_format($product->selling_price, 2, ',', '.') : __('products.edit.no_price') }}
            </p>
            @if($product->profit_margin_percent)
            <p class="text-sm text-slate-500 mt-1">{{ __('products.edit.margin', ['percent' => number_format($product->profit_margin_percent, 1, ',', '.')]) }}</p>
            @endif
        </x-ui.card>

        @if($priceHistories->isNotEmpty())
        <x-ui.card class="p-5">
            <h3 class="font-display font-semibold text-ink mb-3">{{ __('products.edit.price_history') }}</h3>
            <ul class="space-y-2 text-sm">
                @foreach($priceHistories as $history)
                <li class="flex justify-between gap-2 border-b border-slate-100 pb-2 last:border-0">
                    <span class="text-slate-500">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                    <span class="font-semibold">R$ {{ number_format($history->selling_price, 2, ',', '.') }}</span>
                </li>
                @endforeach
            </ul>
        </x-ui.card>
        @endif
    </div>
</div>
@endsection
