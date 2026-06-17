@extends('layouts.auth')
@section('title', __('auth.verify_email.title'))

@section('content')
<div class="max-w-md mx-auto text-center">
    <h1 class="text-xl font-display font-semibold text-ink mb-4">{{ __('auth.verify_email.heading') }}</h1>
    <p class="text-sm text-slate-600 mb-6">{{ __('auth.verify_email.message') }}</p>
    <form method="POST" action="{{ route('tenant.verification.send') }}">
        @csrf
        <x-ui.button type="submit">{{ __('auth.verify_email.resend') }}</x-ui.button>
    </form>
</div>
@endsection
