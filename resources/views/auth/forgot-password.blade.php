@extends('layouts.auth')
@section('title', __('auth.forgot_password.title'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">{{ __('auth.forgot_password.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.forgot_password.subtitle') }}</p>

@if(session('status'))
<x-ui.alert class="ui-alert-success mb-4">{{ session('status') }}</x-ui.alert>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf
    <x-ui.input :label="__('auth.forgot_password.email')" name="email" type="email" required autofocus />
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('auth.forgot_password.submit') }}</x-ui.button>
</form>
@endsection
@section('footer')
<a href="{{ route('login') }}" class="text-brand font-medium hover:underline">{{ __('auth.back_to_login') }}</a>
@endsection
