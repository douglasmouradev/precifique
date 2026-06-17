@extends('layouts.tenant')
@section('title', __('products.title'))
@section('breadcrumb') {{ __('products.breadcrumb') }} @endsection

@section('content')
<x-ui.page-header :title="__('products.title')" :subtitle="request()->boolean('unpriced') ? __('products.index.subtitle_unpriced') : __('products.index.subtitle')">
    <x-slot:actions>
        <x-ui.button :href="route('tenant.products.create')">{{ __('products.index.new_product') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

@if($maxProducts)
@php $atLimit = $productCount >= $maxProducts; @endphp
<div class="mb-6 ui-card-premium p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 {{ $atLimit ? 'ring-2 ring-amber-400/30' : '' }}">
    <div>
        <p class="text-sm font-semibold text-ink">{{ __('products.index.basic_plan_catalog') }}</p>
        <p class="text-sm text-slate-500 mt-0.5">{{ __('products.index.usage', ['count' => $productCount, 'max' => $maxProducts]) }}</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex-1 sm:w-40 h-2 bg-slate-100 rounded-full overflow-hidden min-w-[120px]">
            <div class="h-full rounded-full transition-all {{ $atLimit ? 'bg-amber-500' : 'bg-brand' }}" style="width: {{ min(100, ($productCount / $maxProducts) * 100) }}%"></div>
        </div>
        @if($atLimit)
        <x-ui.button variant="secondary" :href="route('tenant.billing.upgrade')">{{ __('products.index.upgrade') }}</x-ui.button>
        @endif
    </div>
</div>
@endif

<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($products as $product)
    <article class="ui-card-hover overflow-hidden group">
        @if($product->photo_path)
        <div class="aspect-[16/10] overflow-hidden bg-slate-100">
            <img
                src="{{ asset('storage/'.$product->photo_path) }}"
                alt="{{ $product->name }}"
                width="640"
                height="400"
                loading="lazy"
                decoding="async"
                sizes="(max-width: 640px) 100vw, (max-width: 1280px) 50vw, 33vw"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
        </div>
        @else
        <div class="aspect-[16/10] ui-product-placeholder flex items-center justify-center ring-1 ring-inset ring-brand/10">
            <div class="w-14 h-14 rounded-2xl bg-white/70 backdrop-blur-sm flex items-center justify-center shadow-sm ring-1 ring-slate-200/60">
                <x-ui.nav-icon name="products" class="w-7 h-7 text-brand/60" />
            </div>
        </div>
        @endif
        <div class="p-5">
            <h3 class="font-display font-semibold text-lg truncate">{{ $product->name }}</h3>
            <p class="text-2xl font-bold text-brand-dark mt-1">
                {{ $product->selling_price ? 'R$ '.number_format($product->selling_price, 2, ',', '.') : __('products.no_price') }}
            </p>
            @if(!$product->selling_price)
            <span class="ui-badge-brand mt-2">{{ __('products.price_product') }}</span>
            @endif
            <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100 items-center">
                <a href="{{ route('tenant.products.edit', $product) }}" class="text-sm font-semibold text-slate-700 hover:text-brand-dark hover:underline">{{ __('products.index.edit') }}</a>
                <a href="{{ route('tenant.pricing.edit', $product) }}" class="text-sm font-semibold text-brand-dark hover:underline">{{ __('products.price_product') }}</a>
                <form method="POST" action="{{ route('tenant.products.duplicate', $product) }}" class="inline">@csrf
                    <button type="submit" class="text-sm text-slate-600 hover:text-brand-dark font-medium">{{ __('products.index.duplicate') }}</button>
                </form>
                @if($product->selling_price)
                <a href="{{ route('tenant.quotes.pdf', $product) }}" class="text-sm text-slate-600 hover:text-brand-dark font-medium">{{ __('products.index.pdf') }}</a>
                @endif
                <x-ui.confirm-delete
                    :action="route('tenant.products.destroy', $product)"
                    :message="__('products.index.delete_confirm', ['name' => $product->name])"
                />
            </div>
        </div>
    </article>
    @empty
    <div class="sm:col-span-2 xl:col-span-3">
        <x-ui.empty-state
            icon="products"
            :title="__('products.index.empty_title')"
            :description="__('products.index.empty_description')"
        >
            <x-ui.button :href="route('tenant.products.create')">{{ __('products.index.empty_action') }}</x-ui.button>
        </x-ui.empty-state>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
