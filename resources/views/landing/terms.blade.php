@extends('layouts.landing')
@section('title', __('legal.terms.title'))
@section('meta_description', __('legal.terms.meta'))
@section('content')
<article class="max-w-3xl mx-auto py-24 px-4">
    <h1 class="ui-page-title">{{ __('legal.terms.title') }}</h1>
    <div class="mt-6 space-y-6 text-slate-600 leading-relaxed">
        <p>{{ __('legal.terms.intro') }}</p>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.terms.section_service') }}</h2>
            <p class="mt-2">{{ __('legal.terms.section_service_text') }}</p>
        </section>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.terms.section_account') }}</h2>
            <p class="mt-2">{{ __('legal.terms.section_account_text') }}</p>
        </section>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.terms.section_payment') }}</h2>
            <p class="mt-2">{{ __('legal.terms.section_payment_text') }}</p>
        </section>
        <section>
            <h2 class="font-display font-semibold text-ink text-lg">{{ __('legal.terms.section_liability') }}</h2>
            <p class="mt-2">{{ __('legal.terms.section_liability_text') }}</p>
        </section>
    </div>
    <p class="mt-10"><a href="{{ route('home') }}" class="text-brand font-medium hover:underline">{{ __('legal.terms.back') }}</a></p>
</article>
@endsection
