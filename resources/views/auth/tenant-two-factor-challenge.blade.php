@extends('layouts.auth')
@section('title', __('auth.two_factor.title'))

@section('content')
<h1 class="text-xl font-display font-semibold text-ink text-center mb-2">{{ __('auth.two_factor.title') }}</h1>
<p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.two_factor.subtitle') }}</p>

<form method="POST" action="{{ route('tenant.two-factor.challenge.store') }}" class="space-y-4">
    @csrf
    <x-ui.input :label="__('auth.two_factor.code_label')" name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" class="text-center tracking-[0.3em] text-lg" />
    @error('code')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror

    <details class="text-sm text-slate-500">
        <summary class="cursor-pointer font-medium text-slate-600">{{ __('auth.two_factor.use_recovery') }}</summary>
        <div class="mt-3">
            <x-ui.input :label="__('auth.two_factor.recovery_label')" name="recovery_code" autocomplete="off" />
        </div>
    </details>

    <x-ui.button type="submit" class="w-full">{{ __('auth.two_factor.confirm') }}</x-ui.button>
</form>
@endsection
