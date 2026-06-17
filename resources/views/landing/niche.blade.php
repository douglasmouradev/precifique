@extends('layouts.landing')
@section('title', $pageTitle)
@section('meta_description', $pageDescription)

@section('content')
<header class="bg-ink border-b border-white/10">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}"><x-ui.logo variant="full" size="md" dark /></a>
        <div class="flex items-center gap-3">
            <x-ui.locale-switcher dark />
            <a href="{{ route('tenant.login') }}" class="text-sm text-white">{{ __('landing.login') }}</a>
        </div>
    </div>
</header>

<section class="py-20 px-4 max-w-4xl mx-auto text-center bg-ink min-h-[60vh]">
    <h1 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">{{ $pageTitle }}</h1>
    <p class="text-lg text-white/80 mb-8">{{ $pageDescription }}</p>
    <a href="{{ route('tenant.register') }}" class="inline-block bg-brand text-ink px-6 py-3 rounded-xl font-semibold hover:bg-brand-dark">{{ __('landing.cta_start') }}</a>
</section>
@endsection
