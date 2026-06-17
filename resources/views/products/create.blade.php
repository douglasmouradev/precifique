@extends('layouts.tenant')
@section('title', __('products.create.title'))
@section('breadcrumb') {{ __('products.breadcrumb_new') }} @endsection

@section('content')
<x-ui.page-header :title="__('products.create.title')" :subtitle="__('products.create.subtitle')" />

<x-ui.card class="max-w-lg">
    <form method="POST" action="{{ route('tenant.products.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div><label for="product-name" class="ui-label">{{ __('products.create.name') }}</label><input id="product-name" name="name" required class="ui-input"></div>
        <div><label for="product-description" class="ui-label">{{ __('products.create.description') }}</label><textarea id="product-description" name="description" rows="3" class="ui-input"></textarea></div>
        <div>
            <label for="product-niche" class="ui-label">{{ __('products.create.niche') }}</label>
            <select id="product-niche" name="niche_type" class="ui-input">
                <option value="alimentos">{{ __('app.niches.alimentos') }}</option>
                <option value="servico">{{ __('app.niches.servico') }}</option>
                <option value="artesanato">{{ __('app.niches.artesanato') }}</option>
            </select>
        </div>
        <div>
            <label for="product-photo" class="ui-label">{{ __('products.create.photo') }} @if(($tenant->interface_mode ?? '') === 'artesanato')<span class="text-red-500">*</span>@endif</label>
            <input id="product-photo" type="file" name="photo" accept="image/*" class="ui-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand/10 file:text-brand-dark file:font-semibold">
        </div>
        <x-ui.button type="submit" class="w-full py-3">{{ __('products.create.submit') }}</x-ui.button>
    </form>
</x-ui.card>
@endsection
