<x-guest-layout>
    <h1 class="font-display text-xl font-semibold text-center text-slate-700 mb-2">{{ __('auth.two_factor.title') }}</h1>
    <p class="text-sm text-slate-500 text-center mb-6">{{ __('auth.two_factor.subtitle') }}</p>

    <form method="POST" action="{{ route('two-factor.challenge') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="code" :value="__('auth.two_factor.code_label')" />
            <x-text-input id="code" class="block mt-1 w-full text-center tracking-[0.3em] text-lg" type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-2.5">
            {{ __('auth.two_factor.confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
