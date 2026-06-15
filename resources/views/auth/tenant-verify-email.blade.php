@extends('layouts.auth')
@section('title', __('Verify Email Address'))

@section('content')
<div class="max-w-md mx-auto text-center">
    <h1 class="text-xl font-display font-semibold text-ink mb-4">{{ __('Verify Email Address') }}</h1>
    <p class="text-sm text-slate-600 mb-6">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
    <form method="POST" action="{{ route('tenant.verification.send') }}">
        @csrf
        <x-ui.button type="submit">{{ __('Resend Verification Email') }}</x-ui.button>
    </form>
</div>
@endsection
