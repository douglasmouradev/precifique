<section>
    <h2 class="ui-section-title">{{ __('profile.information_title') }}</h2>
    <p class="text-sm text-slate-500 mt-1">{{ __('profile.information_subtitle') }}</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <x-ui.input :label="__('profile.name')" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />

        <x-ui.input :label="__('profile.email')" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
            <p>{{ __('profile.unverified_email') }}</p>
            <button form="send-verification" class="mt-2 font-semibold underline">{{ __('profile.resend_verification') }}</button>
            @if (session('status') === 'verification-link-sent')
            <p class="mt-2 text-emerald-700 font-medium">{{ __('profile.verification_sent') }}</p>
            @endif
        </div>
        @endif

        <div class="flex items-center gap-4 pt-2">
            <x-ui.button type="submit">{{ __('profile.save') }}</x-ui.button>
            @if (session('status') === 'profile-updated')
            <p data-saved-flash class="text-sm text-emerald-600">{{ __('profile.saved') }}</p>
            @endif
        </div>
    </form>
</section>
