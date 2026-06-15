@extends('layouts.onboarding', ['step' => 2])
@section('title', __('onboarding.mode.title'))
@section('content')
<h1 class="font-display text-xl sm:text-2xl font-bold text-center mb-2">{{ __('onboarding.mode.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-8">{{ __('onboarding.mode.subtitle') }}</p>

<form method="POST" action="{{ route('onboarding.mode.store') }}" class="grid sm:grid-cols-2 gap-4">
    @csrf
    <label class="p-6 ui-card border-2 border-transparent cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-colors">
        <input type="radio" name="usage_mode" value="iniciante" class="sr-only" required @checked(old('usage_mode', 'iniciante') === 'iniciante')>
        <div class="w-10 h-10 rounded-lg bg-brand/10 text-brand flex items-center justify-center mb-3">
            <x-ui.nav-icon name="spark" class="w-5 h-5" />
        </div>
        <p class="font-bold">{{ __('onboarding.mode.beginner.label') }}</p>
        <p class="text-sm text-slate-500 mt-2">{{ __('onboarding.mode.beginner.description') }}</p>
    </label>
    <label class="p-6 ui-card border-2 border-transparent cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-colors">
        <input type="radio" name="usage_mode" value="avancado" class="sr-only" @checked(old('usage_mode') === 'avancado')>
        <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center mb-3">
            <x-ui.nav-icon name="reports" class="w-5 h-5" />
        </div>
        <p class="font-bold">{{ __('onboarding.mode.advanced.label') }}</p>
        <p class="text-sm text-slate-500 mt-2">{{ __('onboarding.mode.advanced.description') }}</p>
    </label>
    <x-ui.button variant="secondary" type="submit" class="sm:col-span-2 py-3">{{ __('onboarding.continue') }}</x-ui.button>
</form>
@endsection
