@extends('layouts.tenant')
@section('title', __('app.menu_page.title'))
@section('breadcrumb') {{ __('app.menu_page.title') }} @endsection

@section('content')
<x-ui.page-header :title="__('app.menu_page.title')" :subtitle="__('app.menu_page.subtitle')" />

<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-w-2xl">
    @foreach($links as $link)
    @php
        $href = isset($link['query'])
            ? route($link['route'], $link['query'])
            : route($link['route']);
        $isActive = request()->fullUrlIs($href) || request()->routeIs($link['route']);
    @endphp
    <a href="{{ $href }}"
       class="ui-card p-4 flex flex-col items-center gap-2 text-center hover:border-brand/40 transition-colors {{ $isActive ? 'ring-2 ring-brand/30' : '' }}">
        <x-ui.nav-icon :name="$link['icon']" class="w-7 h-7 text-brand-dark" />
        <span class="text-sm font-semibold text-ink">{{ $link['label'] }}</span>
    </a>
    @endforeach
</div>
@endsection
