<x-app-layout>
    <x-ui.page-header :title="__('Profile')" :subtitle="__('admin.nav.my_profile')" class="mb-6" />

    <div class="space-y-6 max-w-2xl">
        <x-ui.card>
            @include('profile.partials.update-profile-information-form')
        </x-ui.card>

        @if(auth()->user()?->is_superadmin)
        <x-ui.card>
            <h3 class="ui-section-title">{{ __('app.account.two_factor') }}</h3>
            <p class="text-sm text-slate-600 mt-1">{{ __('auth.two_factor.admin_required') }}</p>
            <div class="mt-4">
                <x-ui.button :href="route('profile.two-factor')" variant="outline">
                    {{ auth()->user()->hasTwoFactorEnabled() ? __('app.account.disable_2fa') : __('app.account.enable_2fa') }}
                </x-ui.button>
            </div>
        </x-ui.card>
        @endif

        <x-ui.card>
            @include('profile.partials.update-password-form')
        </x-ui.card>

        <x-ui.card>
            @include('profile.partials.delete-user-form')
        </x-ui.card>
    </div>
</x-app-layout>
