@extends('layouts.landing')
@section('title', __('legal.privacy.title'))
@section('meta_description', __('legal.privacy.meta'))
@section('content')
<x-landing.header />
<article class="max-w-3xl mx-auto pt-[6.5rem] pb-20 px-4">
    <div class="landing-card">
        <h1 class="ui-page-title">{{ __('legal.privacy.title') }}</h1>
        <p class="ui-page-subtitle mt-2">{{ __('legal.privacy.meta') }}</p>
        <div class="mt-8 space-y-8 text-slate-600 leading-relaxed">
            <p>{{ __('legal.privacy.intro') }}</p>
            @foreach(['section_data', 'section_use', 'section_rights', 'section_security'] as $section)
            <section>
                <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.privacy.'.$section) }}</h2>
                <p class="mt-2">{{ __('legal.privacy.'.$section.'_text') }}</p>
            </section>
            @endforeach
        </div>
        <p class="mt-10 pt-6 border-t border-slate-100">
            <a href="{{ route('home') }}" class="text-brand font-semibold hover:underline">{{ __('legal.privacy.back') }}</a>
        </p>
    </div>
</article>
<x-landing.footer />
@endsection
