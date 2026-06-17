<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.account.two_factor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg max-w-lg">
                @if (session('warning'))
                    <p class="mb-4 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">{{ session('warning') }}</p>
                @endif

                @if($enabled)
                <p class="text-sm text-slate-600 mb-4">{{ __('app.account.two_factor_active') }}</p>
                <form method="POST" action="{{ route('profile.two-factor.destroy') }}" class="space-y-4">
                    @csrf @method('DELETE')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('app.account.current_password') }}</label>
                        <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        {{ __('app.account.disable_2fa') }}
                    </button>
                </form>
                @else
                <p class="text-sm text-slate-600 mb-4">{{ __('auth.two_factor.admin_required') }}</p>
                <p class="text-sm text-slate-600 mb-4">{{ __('app.account.two_factor_scan') }}</p>
                <div class="bg-white p-4 rounded-lg inline-block mb-4 ring-1 ring-slate-200">
                    <canvas id="two-factor-qr-canvas" data-qr-uri="{{ $qrUri }}" width="180" height="180" aria-label="{{ __('app.account.two_factor_scan') }}"></canvas>
                </div>
                <form method="POST" action="{{ route('profile.two-factor.confirm') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('auth.two_factor.code_label') }}</label>
                        <input name="code" maxlength="6" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" inputmode="numeric" autocomplete="one-time-code">
                        @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">
                        {{ __('auth.two_factor.confirm') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    @vite('resources/js/two-factor-qr.js')
    @endpush
</x-app-layout>
