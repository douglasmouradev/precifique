@extends('layouts.auth')
@section('title', __('auth.register.title'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">{{ __('auth.register.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.register.legacy_redirect') }}</p>
<p class="text-sm text-brand-dark font-medium text-center mb-6">{{ __('auth.register.subtitle') }}</p>

<div class="flex flex-col gap-3">
    <x-ui.button :href="route('tenant.register')" class="w-full">{{ __('auth.register.go_register') }}</x-ui.button>
    <p class="text-center text-sm text-slate-500">
        {{ __('auth.register.has_account') }}
        <a href="{{ route('tenant.login') }}" class="text-brand font-semibold hover:underline">{{ __('auth.register.sign_in') }}</a>
    </p>
</div>
@endsection
