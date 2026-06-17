<section>
    <h2 class="ui-section-title">{{ __('profile.password_title') }}</h2>
    <p class="text-sm text-slate-500 mt-1">{{ __('profile.password_subtitle') }}</p>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('put')

        <x-ui.input :label="__('profile.current_password')" name="current_password" type="password" autocomplete="current-password" />
        @error('current_password', 'updatePassword')
        <p class="text-red-600 text-sm -mt-2">{{ $message }}</p>
        @enderror

        <x-ui.input :label="__('profile.new_password')" name="password" type="password" autocomplete="new-password" />
        @error('password', 'updatePassword')
        <p class="text-red-600 text-sm -mt-2">{{ $message }}</p>
        @enderror

        <x-ui.input :label="__('profile.confirm_password')" name="password_confirmation" type="password" autocomplete="new-password" />

        <div class="flex items-center gap-4 pt-2">
            <x-ui.button type="submit">{{ __('profile.save') }}</x-ui.button>
            @if (session('status') === 'password-updated')
            <p data-saved-flash class="text-sm text-emerald-600">{{ __('profile.saved') }}</p>
            @endif
        </div>
    </form>
</section>
