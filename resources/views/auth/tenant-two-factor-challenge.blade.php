@extends('layouts.auth')
@section('title', __('auth.two_factor.title'))

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-xl font-display font-semibold text-ink mb-4">{{ __('auth.two_factor.title') }}</h1>
    <form method="POST" action="{{ route('tenant.two-factor.challenge.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="ui-label">{{ __('auth.two_factor.code_label') }}</label>
            <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required class="ui-input text-center tracking-widest" autocomplete="one-time-code">
            @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <x-ui.button type="submit" class="w-full">{{ __('auth.two_factor.confirm') }}</x-ui.button>
    </form>
</div>
@endsection
