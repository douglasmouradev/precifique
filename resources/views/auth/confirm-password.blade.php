@extends('layouts.auth')
@section('title', __('auth.confirm_password.title'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">{{ __('auth.confirm_password.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.confirm_password.subtitle') }}</p>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
    @csrf
    <x-ui.input :label="__('auth.login.password')" name="password" type="password" required autocomplete="current-password" />
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('auth.confirm_password.submit') }}</x-ui.button>
</form>
@endsection
