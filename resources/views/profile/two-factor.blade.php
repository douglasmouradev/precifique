<x-app-layout>
    <x-ui.page-header :title="__('app.account.two_factor')" :subtitle="__('auth.two_factor.admin_required')" class="mb-6" />

    <div class="max-w-lg">
        <x-ui.card>
            @if($enabled)
            <p class="text-sm text-slate-600 mb-4">{{ __('app.account.two_factor_active') }}</p>
            <form method="POST" action="{{ route('profile.two-factor.destroy') }}" class="space-y-4">
                @csrf @method('DELETE')
                <x-ui.input :label="__('app.account.current_password')" name="password" type="password" required />
                <x-ui.button type="submit" variant="outline">{{ __('app.account.disable_2fa') }}</x-ui.button>
            </form>
            @else
            <p class="text-sm text-slate-600 mb-4">{{ __('app.account.two_factor_scan') }}</p>
            <div class="bg-slate-50 p-4 rounded-xl inline-block mb-4 ring-1 ring-slate-200">
                <canvas id="two-factor-qr-canvas" data-qr-uri="{{ $qrUri }}" width="180" height="180" aria-label="{{ __('app.account.two_factor_scan') }}"></canvas>
            </div>
            <form method="POST" action="{{ route('profile.two-factor.confirm') }}" class="space-y-4">
                @csrf
                <x-ui.input :label="__('auth.two_factor.code_label')" name="code" maxlength="6" required inputmode="numeric" autocomplete="one-time-code" />
                @error('code')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                <x-ui.button type="submit">{{ __('auth.two_factor.confirm') }}</x-ui.button>
            </form>
            @endif
        </x-ui.card>
    </div>

    @push('scripts')
    @vite('resources/js/two-factor-qr.js')
    @endpush
</x-app-layout>
