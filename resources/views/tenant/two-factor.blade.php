@extends('layouts.tenant')
@section('title', __('app.account.two_factor'))

@section('content')
<x-ui.page-header :title="__('app.account.two_factor')" />

<div class="max-w-lg">
    <x-ui.card>
        @if($enabled)
        <p class="text-sm text-slate-600 mb-4">{{ __('app.account.two_factor_active') }}</p>
        <form method="POST" action="{{ route('tenant.account.two-factor.destroy') }}" class="space-y-4">
            @csrf @method('DELETE')
            <div>
                <label class="ui-label">{{ __('app.account.current_password') }}</label>
                <input type="password" name="password" required class="ui-input">
            </div>
            <x-ui.button type="submit" variant="outline">{{ __('app.account.disable_2fa') }}</x-ui.button>
        </form>
        @else
        <p class="text-sm text-slate-600 mb-4">{{ __('app.account.two_factor_scan') }}</p>
        <div class="bg-white p-4 rounded-lg inline-block mb-4">
            <canvas id="two-factor-qr-canvas" data-qr-uri="{{ $qrUri }}" width="180" height="180" aria-label="{{ __('app.account.two_factor_scan') }}"></canvas>
        </div>
        <form method="POST" action="{{ route('tenant.account.two-factor.confirm') }}" class="space-y-4">
            @csrf
            <div>
                <label class="ui-label">{{ __('auth.two_factor.code_label') }}</label>
                <input name="code" maxlength="6" required class="ui-input" inputmode="numeric" autocomplete="one-time-code">
                @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <x-ui.button type="submit">{{ __('auth.two_factor.confirm') }}</x-ui.button>
        </form>
        @endif
    </x-ui.card>
</div>
@endsection

@push('scripts')
@vite('resources/js/two-factor-qr.js')
@endpush
