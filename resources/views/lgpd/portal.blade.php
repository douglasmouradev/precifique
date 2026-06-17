@extends('layouts.tenant')
@section('title', __('lgpd.portal.title'))
@section('breadcrumb') {{ __('lgpd.portal.title') }} @endsection

@section('content')
<x-ui.page-header :title="__('lgpd.portal.heading')" :subtitle="__('lgpd.portal.subtitle')" />

<div class="max-w-xl space-y-4">
    <x-ui.card>
        <p class="text-sm text-slate-600 mb-4">{{ __('lgpd.portal.export_intro') }}</p>
        <x-ui.button variant="outline" :href="route('tenant.lgpd.export')" class="w-full justify-center">{{ __('lgpd.portal.export_button') }}</x-ui.button>
    </x-ui.card>

    <x-ui.card>
        <h2 class="ui-section-title text-red-700">{{ __('lgpd.portal.delete_heading') }}</h2>
        <form method="POST" action="{{ route('tenant.lgpd.destroy') }}" data-confirm="{{ __('lgpd.portal.delete_confirm_prompt') }}" class="space-y-3">
            @csrf @method('DELETE')
            <div>
                <label for="lgpd-confirm" class="ui-label">{{ __('lgpd.portal.delete_confirm_label') }}</label>
                <input id="lgpd-confirm" name="confirm" class="ui-input" autocomplete="off" required>
            </div>
            <div>
                <label for="lgpd-password" class="ui-label">{{ __('lgpd.portal.delete_password_label') }}</label>
                <input id="lgpd-password" type="password" name="password" class="ui-input" required autocomplete="current-password">
                @error('password')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <x-ui.button type="submit" variant="outline" class="text-red-600 border-red-200 hover:bg-red-50">{{ __('lgpd.portal.delete_submit') }}</x-ui.button>
        </form>
    </x-ui.card>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('form[data-confirm]')?.addEventListener('submit', function (e) {
    if (!confirm(this.dataset.confirm)) e.preventDefault();
});
</script>
@endpush
