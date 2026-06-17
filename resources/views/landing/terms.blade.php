@extends('layouts.landing')
@section('title', __('legal.terms.title'))
@section('meta_description', __('legal.terms.meta'))
@section('content')
<x-landing.header />
<article class="max-w-3xl mx-auto pt-[6.5rem] pb-20 px-4">
    <div class="landing-card">
        <h1 class="ui-page-title">{{ __('legal.terms.title') }}</h1>
        <p class="ui-page-subtitle mt-2">{{ __('legal.terms.meta') }}</p>
        <div class="mt-8 space-y-8 text-slate-600 leading-relaxed">
            <p>{{ __('legal.terms.intro') }}</p>
            @foreach(['section_service', 'section_account', 'section_payment', 'section_liability'] as $section)
            <section>
                <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.terms.'.$section) }}</h2>
                <p class="mt-2">{{ __('legal.terms.'.$section.'_text') }}</p>
            </section>
            @endforeach
        </div>
        <p class="mt-10 pt-6 border-t border-slate-100">
            <a href="{{ route('home') }}" class="text-brand font-semibold hover:underline">{{ __('legal.terms.back') }}</a>
        </p>
    </div>
</article>
<x-landing.footer />
@endsection
