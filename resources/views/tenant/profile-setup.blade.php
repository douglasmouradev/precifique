@extends('layouts.tenant-minimal')
@section('title', __('profile_setup.title'))

@section('content')
<div class="max-w-3xl mx-auto py-8 md:py-12 px-4">
    <div class="text-center mb-8">
        <span class="ui-badge-brand mb-4 inline-block">{{ __('profile_setup.badge') }}</span>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-ink">{{ __('profile_setup.heading') }}</h1>
        <p class="text-slate-500 mt-2 text-sm md:text-base max-w-lg mx-auto">
            {{ __('profile_setup.subtitle') }}
        </p>
    </div>

    <x-ui.card class="p-6 md:p-8">
        <form method="POST" action="{{ route('tenant.profile.setup.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="ui-label">{{ __('profile_setup.business_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="ui-input" placeholder="{{ __('profile_setup.business_name_placeholder') }}">
            </div>

            <div>
                <label class="ui-label mb-3 block">{{ __('profile_setup.niche_label') }}</label>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach([
                        ['alimentos', 'food'],
                        ['servico', 'service'],
                        ['artesanato', 'craft'],
                        ['outro', 'edit'],
                    ] as [$nicheKey, $icon])
                    <label class="cursor-pointer block p-4 rounded-xl border-2 transition-colors
                        {{ ($selectedNiche ?? '') === $nicheKey ? 'border-brand bg-brand/5' : 'border-slate-200 hover:border-brand/40' }}">
                        <input type="radio" name="niche" value="{{ $nicheKey }}" class="sr-only" required
                            @checked(old('niche', $selectedNiche) === $nicheKey)>
                        <div class="w-10 h-10 rounded-lg bg-brand/10 text-brand flex items-center justify-center mb-2">
                            <x-ui.nav-icon :name="$icon" class="w-5 h-5" />
                        </div>
                        <p class="font-semibold text-sm">{{ __('profile_setup.niches.'.$nicheKey.'.label') }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('profile_setup.niches.'.$nicheKey.'.description') }}</p>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="ui-label">{{ __('profile_setup.niche_other_label') }}</label>
                <input type="text" name="niche_other" value="{{ old('niche_other', $tenant->niche_metadata['other'] ?? '') }}" class="ui-input" placeholder="{{ __('profile_setup.niche_other_placeholder') }}">
            </div>

            <x-ui.button type="submit" variant="secondary" class="w-full py-3 text-base">
                {{ __('profile_setup.submit') }}
            </x-ui.button>
        </form>
    </x-ui.card>
</div>
@endsection
