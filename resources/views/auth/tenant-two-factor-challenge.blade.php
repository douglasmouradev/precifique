@extends('layouts.auth')
@section('title', __('Two-factor authentication'))

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-xl font-display font-semibold text-ink mb-4">{{ __('Two-factor authentication') }}</h1>
    <form method="POST" action="{{ route('tenant.two-factor.challenge.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="ui-label">{{ __('Authentication code') }}</label>
            <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required class="ui-input text-center tracking-widest" autocomplete="one-time-code">
            @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <x-ui.button type="submit" class="w-full">{{ __('Confirm') }}</x-ui.button>
    </form>
</div>
@endsection
