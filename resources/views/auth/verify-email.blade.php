@extends('layouts.auth')
@section('title', __('auth.verify_email.title'))
@section('content')
<h1 class="font-display text-xl font-semibold text-center mb-2">{{ __('auth.verify_email.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.verify_email.subtitle') }}</p>

@if(session('status') === 'verification-link-sent')
<x-ui.alert class="ui-alert-success mb-4">{{ __('auth.verify_email.link_sent') }}</x-ui.alert>
@endif

<div class="flex flex-col gap-4">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('auth.verify_email.resend') }}</x-ui.button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full text-sm text-slate-500 hover:text-brand transition-colors">{{ __('auth.verify_email.logout') }}</button>
    </form>
</div>
@endsection
