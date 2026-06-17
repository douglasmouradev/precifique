@extends('layouts.landing')
@section('title', $pageTitle)
@section('meta_description', $pageDescription)

@section('content')
<x-landing.header />
<section class="landing-section bg-ink text-white relative overflow-hidden pt-[4.25rem]">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_70%_50%_at_50%_0%,rgba(0,200,150,0.4),transparent)]"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative">
        <p class="landing-eyebrow mb-4">{{ __('landing.hero_eyebrow') }}</p>
        <h1 class="landing-section-title text-white mb-6">{{ $pageTitle }}</h1>
        <p class="text-lg text-slate-300 mb-10 max-w-2xl mx-auto leading-relaxed">{{ $pageDescription }}</p>
        <a href="{{ route('tenant.register') }}" class="landing-btn-brand-lg">{{ __('landing.cta_start') }}</a>
    </div>
</section>
<section class="landing-section bg-paper">
    <div class="max-w-4xl mx-auto px-4 grid sm:grid-cols-3 gap-6">
        @foreach(__('landing.solution_features') as $feature)
        <div class="landing-card text-center !p-6">
            <span class="w-8 h-8 rounded-full bg-brand/15 text-brand flex items-center justify-center mx-auto mb-3 text-sm font-bold">✓</span>
            <p class="text-sm text-slate-600">{{ $feature }}</p>
        </div>
        @endforeach
    </div>
</section>
<x-landing.footer />
@endsection
