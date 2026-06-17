<x-guest-layout>
    <h1 class="font-display text-xl font-semibold text-center text-slate-700 mb-2">{{ __('auth.two_factor.title') }}</h1>
    <p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.two_factor.subtitle') }}</p>

    <form method="POST" action="{{ route('two-factor.challenge') }}" class="space-y-5">
        @csrf

        <div>
            <x-ui.input :label="__('auth.two_factor.code_label')" name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" class="text-center tracking-[0.3em] text-lg" />
            @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <details class="text-sm text-slate-500">
            <summary class="cursor-pointer font-medium text-slate-600">{{ __('auth.two_factor.use_recovery') }}</summary>
            <div class="mt-3">
                <x-ui.input :label="__('auth.two_factor.recovery_label')" name="recovery_code" autocomplete="off" />
            </div>
        </details>

        <x-ui.button type="submit" class="w-full py-3">{{ __('auth.two_factor.confirm') }}</x-ui.button>
    </form>
</x-guest-layout>
