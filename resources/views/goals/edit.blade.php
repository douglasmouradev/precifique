@extends('layouts.tenant')
@section('title', __('goals.title'))
@section('breadcrumb') {{ __('goals.breadcrumb') }} @endsection

@section('content')
<x-ui.page-header :title="__('goals.page_title')" :subtitle="__('goals.subtitle')" />

<x-ui.card class="max-w-md">
    <form method="POST" action="{{ route('tenant.goals.store') }}" class="space-y-5">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('goals.year') }}</label><input type="number" name="year" value="{{ $goal?->year ?? now()->year }}" class="ui-input"></div>
            <div><label class="ui-label">{{ __('goals.month') }}</label><input type="number" name="month" min="1" max="12" value="{{ $goal?->month ?? now()->month }}" class="ui-input"></div>
        </div>
        <div><label class="ui-label">{{ __('goals.goal_amount') }}</label><input type="number" name="goal_amount" step="0.01" value="{{ $goal?->goal_amount }}" required placeholder="{{ __('goals.goal_placeholder') }}" class="ui-input"></div>
        <x-ui.button type="submit" class="w-full py-3">{{ __('goals.submit') }}</x-ui.button>
    </form>
</x-ui.card>
@endsection
