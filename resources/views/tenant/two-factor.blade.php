@extends('layouts.tenant')
@section('title', __('app.account.two_factor'))

@section('content')
<x-ui.page-header :title="__('app.account.two_factor')" />

<div class="max-w-lg">
    <x-ui.card>
        @if($enabled)
        <p class="text-sm text-slate-600 mb-4">2FA ativo nesta conta.</p>
        <form method="POST" action="{{ route('tenant.account.two-factor.destroy') }}" class="space-y-4">
            @csrf @method('DELETE')
            <div>
                <label class="ui-label">{{ __('app.account.current_password') }}</label>
                <input type="password" name="password" required class="ui-input">
            </div>
            <x-ui.button type="submit" variant="outline">{{ __('app.account.disable_2fa') }}</x-ui.button>
        </form>
        @else
        <p class="text-sm text-slate-600 mb-4">Escaneie o QR code no Google Authenticator ou similar.</p>
        <div class="bg-white p-4 rounded-lg inline-block mb-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrUri) }}" alt="QR 2FA" width="180" height="180">
        </div>
        <p class="text-xs text-slate-400 mb-4 break-all">Secret: {{ $secret }}</p>
        <form method="POST" action="{{ route('tenant.account.two-factor.confirm') }}" class="space-y-4">
            @csrf
            <div>
                <label class="ui-label">{{ __('Authentication code') }}</label>
                <input name="code" maxlength="6" required class="ui-input">
                @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <x-ui.button type="submit">{{ __('Confirm') }}</x-ui.button>
        </form>
        @endif
    </x-ui.card>
</div>
@endsection
