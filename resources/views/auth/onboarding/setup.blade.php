@extends('layouts.onboarding', ['step' => 4])
@section('title', __('onboarding.setup.title'))
@section('content')
<h1 class="font-display text-xl sm:text-2xl font-bold mb-2">{{ __('onboarding.setup.heading') }}</h1>
<p class="text-sm text-slate-500 mb-8">{{ __('onboarding.setup.subtitle') }}</p>

<form method="POST" action="{{ route('onboarding.setup.store') }}" enctype="multipart/form-data" class="ui-card p-6 sm:p-8 space-y-4">
    @csrf
    <x-ui.input :label="__('onboarding.setup.business_name')" name="name" value="{{ old('name', auth('tenant')->user()->name) }}" required />
    <div>
        <label class="ui-label">{{ __('onboarding.setup.logo_optional') }}</label>
        <input type="file" name="logo" accept="image/*" class="ui-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand/10 file:text-brand-dark file:font-semibold">
    </div>
    <x-ui.input :label="__('onboarding.setup.fixed_cost_label')" name="fixed_cost_name" value="{{ old('fixed_cost_name') }}" required :placeholder="__('onboarding.setup.fixed_cost_placeholder')" />
    <x-ui.input :label="__('onboarding.setup.monthly_amount')" name="fixed_cost_amount" type="number" step="0.01" value="{{ old('fixed_cost_amount') }}" required />
    <x-ui.button variant="secondary" type="submit" class="w-full py-3">{{ __('onboarding.setup.submit') }}</x-ui.button>
</form>
@endsection
