@extends('layouts.tenant')
@section('title', 'Menu')
@section('breadcrumb') Menu @endsection

@section('content')
<x-ui.page-header title="Menu" subtitle="Acesso rápido a todas as áreas" />

<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-w-2xl">
    @foreach($links as $link)
    <a href="{{ route($link['route']) }}"
       class="ui-card p-4 flex flex-col items-center gap-2 text-center hover:border-brand/40 transition-colors {{ request()->routeIs($link['route']) ? 'ring-2 ring-brand/30' : '' }}">
        <x-ui.nav-icon :name="$link['icon']" class="w-7 h-7 text-brand-dark" />
        <span class="text-sm font-semibold text-ink">{{ $link['label'] }}</span>
    </a>
    @endforeach
</div>
@endsection
