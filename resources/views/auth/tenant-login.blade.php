@extends('layouts.auth')
@section('title', __('auth.login.title'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">{{ __('auth.login.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.login.subtitle') }}</p>

<form method="POST" action="{{ route('tenant.login.store') }}" class="space-y-4">
    @csrf
    <x-ui.input :label="__('auth.login.email')" name="email" type="email" value="{{ old('email') }}" required autofocus />
    <x-ui.input :label="__('auth.login.password')" name="password" type="password" required />
    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand focus:ring-brand/30">
        {{ __('auth.login.remember_me') }}
    </label>
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('auth.login.submit') }}</x-ui.button>
    <x-ui.turnstile class="mt-2" />
</form>
@endsection
@section('footer')
<p><a href="{{ route('tenant.password.request') }}" class="text-brand font-medium hover:underline">{{ __('auth.login.forgot_password') }}</a></p>
<p class="mt-2">{{ __('auth.login.no_account') }} <a href="{{ route('tenant.register') }}" class="text-brand font-semibold hover:underline">{{ __('auth.login.register_free') }}</a></p>
@endsection
