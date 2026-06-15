@extends('layouts.onboarding', ['step' => 3])
@section('title', __('onboarding.plan.title'))
@section('content')
<h1 class="font-display text-xl sm:text-2xl font-bold text-center mb-2">{{ __('onboarding.plan.heading') }}</h1>
<p class="text-sm text-slate-500 text-center mb-8">{{ __('onboarding.plan.subtitle', ['days' => config('tenancy.trial_days', 14)]) }}</p>

<form method="POST" action="{{ route('onboarding.plan.store') }}" class="grid sm:grid-cols-2 gap-4">
    @csrf
    @foreach($plans as $plan)
    <label class="p-6 ui-card border-2 border-transparent cursor-pointer has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-colors {{ $plan->slug === 'premium' ? 'ring-1 ring-brand/20' : '' }}">
        <input type="radio" name="plan" value="{{ $plan->slug }}" class="sr-only" required @checked(old('plan', 'basic') === $plan->slug)>
        @if($plan->slug === 'premium')
        <span class="ui-badge-premium mb-2">{{ __('billing.recommended') }}</span>
        @endif
        <p class="font-bold text-lg">{{ $plan->name }}</p>
        <p class="text-2xl font-display font-bold mt-1">
            R$ {{ number_format((float) $plan->price_monthly, 2, ',', '.') }}
            <span class="text-sm font-normal text-slate-500">{{ __('billing.per_month') }}</span>
        </p>
        <ul class="mt-4 space-y-1.5 text-sm text-slate-600">
            @foreach(array_slice($plan->features ?? [], 0, 3) as $feature)
            <li class="flex gap-2"><span class="text-brand">✓</span>{{ $feature }}</li>
            @endforeach
        </ul>
    </label>
    @endforeach
    <x-ui.button variant="secondary" type="submit" class="sm:col-span-2 py-3">{{ __('onboarding.continue') }}</x-ui.button>
</form>
@endsection
