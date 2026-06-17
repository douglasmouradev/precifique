@extends('layouts.landing')
@section('title', __('legal.privacy.title'))
@section('meta_description', __('legal.privacy.meta'))
@section('content')
<article class="max-w-3xl mx-auto py-24 px-4">
    <h1 class="ui-page-title">{{ __('legal.privacy.title') }}</h1>
    <div class="mt-6 space-y-6 text-slate-600 leading-relaxed">
        <p>{{ __('legal.privacy.intro') }}</p>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.privacy.section_data') }}</h2>
            <p class="mt-2">{{ __('legal.privacy.section_data_text') }}</p>
        </section>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.privacy.section_use') }}</h2>
            <p class="mt-2">{{ __('legal.privacy.section_use_text') }}</p>
        </section>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.privacy.section_rights') }}</h2>
            <p class="mt-2">{{ __('legal.privacy.section_rights_text') }}</p>
        </section>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.privacy.section_security') }}</h2>
            <p class="mt-2">{{ __('legal.privacy.section_security_text') }}</p>
        </section>
    </div>
    <p class="mt-10"><a href="{{ route('home') }}" class="text-brand font-medium hover:underline">{{ __('legal.privacy.back') }}</a></p>
</article>
@endsection
