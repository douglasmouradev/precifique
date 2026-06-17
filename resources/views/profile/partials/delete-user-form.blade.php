<section class="space-y-4">
    <div>
        <h2 class="ui-section-title text-red-700">{{ __('profile.delete_title') }}</h2>
        <p class="text-sm text-slate-500 mt-1">{{ __('profile.delete_subtitle') }}</p>
    </div>

    <x-ui.button variant="outline" type="button" data-modal-open="confirm-user-deletion" class="border-red-200 text-red-700 hover:bg-red-50">
        {{ __('profile.delete_button') }}
    </x-ui.button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-ink">{{ __('profile.delete_confirm_title') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ __('profile.delete_confirm_subtitle') }}</p>

            <div class="mt-4">
                <x-ui.input :label="__('profile.current_password')" name="password" type="password" autocomplete="current-password" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button variant="outline" type="button" data-modal-close>{{ __('profile.cancel') }}</x-ui.button>
                <x-ui.button type="submit" class="bg-red-600 hover:bg-red-700 text-white border-red-600">{{ __('profile.delete_button') }}</x-ui.button>
            </div>
        </form>
    </x-modal>
</section>
