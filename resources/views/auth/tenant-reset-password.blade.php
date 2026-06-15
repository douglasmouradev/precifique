@extends('layouts.auth')
@section('title', __('auth.reset_password.title'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-6">{{ __('auth.reset_password.heading') }}</h1>

<form method="POST" action="{{ route('tenant.password.store') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <x-ui.input :label="__('auth.reset_password.email')" name="email" type="email" value="{{ old('email', $email) }}" required />
    <x-ui.input :label="__('auth.reset_password.new_password')" name="password" type="password" required />
    <x-ui.input :label="__('auth.reset_password.password_confirmation')" name="password_confirmation" type="password" required />
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('auth.reset_password.submit') }}</x-ui.button>
</form>
@endsection
