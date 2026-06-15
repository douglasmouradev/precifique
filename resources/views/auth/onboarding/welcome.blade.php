@extends('layouts.onboarding')
@section('title', __('onboarding.welcome_title'))
@section('content')
<div class="text-center">
    <h1 class="font-display text-2xl sm:text-3xl font-bold max-w-lg mx-auto">{{ __('onboarding.welcome_heading') }}</h1>
    <p class="text-slate-500 mt-4 max-w-md mx-auto">{{ __('onboarding.welcome_text') }}</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center mt-10">
        <x-ui.button variant="secondary" :href="route('onboarding.niche')" class="px-8 py-3.5">{{ __('onboarding.want_learn') }}</x-ui.button>
        <x-ui.button variant="outline" :href="route('onboarding.skip')" class="px-8 py-3.5">{{ __('onboarding.already_know') }}</x-ui.button>
    </div>
</div>
@endsection
