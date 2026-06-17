@extends('layouts.auth')
@section('title', __('auth.admin_login.heading'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">{{ __('auth.admin_login.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">
    {{ __('auth.admin_login.tenant_hint') }}
    <a href="{{ route('tenant.login') }}" class="text-brand font-semibold hover:underline">{{ __('auth.admin_login.tenant_link') }}</a>
</p>

@if($errors->has('email') && $errors->first('email') === __('auth.tenant_login_hint'))
<x-ui.alert class="ui-alert-warning mb-4">
    {{ __('auth.tenant_login_hint') }}
    <a href="{{ route('tenant.login') }}" class="mt-2 block font-semibold text-brand-dark hover:underline">{{ __('auth.admin_login.go_tenant_login') }}</a>
</x-ui.alert>
@endif

@if(session('status'))
<x-ui.alert class="ui-alert-success mb-4">{{ session('status') }}</x-ui.alert>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf
    <x-ui.input :label="__('auth.login.email')" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
    <x-ui.input :label="__('auth.login.password')" name="password" type="password" required autocomplete="current-password" />
    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand focus:ring-brand/30">
        {{ __('auth.login.remember_me') }}
    </label>
    @if(config('security.turnstile.secret_key'))
    <x-ui.turnstile />
    @endif
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        @if (Route::has('password.request'))
        <a class="text-sm text-slate-500 hover:text-brand" href="{{ route('password.request') }}">{{ __('auth.login.forgot_password') }}</a>
        @endif
        <x-ui.button type="submit" class="w-full sm:w-auto">{{ __('auth.login.submit') }}</x-ui.button>
    </div>
</form>
@endsection
