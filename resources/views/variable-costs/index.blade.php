@extends('layouts.tenant')
@section('title', __('costs.variable.title'))
@section('breadcrumb') {{ __('costs.variable.breadcrumb') }} @endsection

@section('content')
<x-ui.page-header :title="__('costs.variable.page_title')" :subtitle="__('costs.variable.subtitle')">
    <x-slot:actions>
        <x-ui.button variant="outline" :href="route('tenant.fixed-costs.index')">{{ __('costs.variable.fixed_costs_link') }}</x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.upgrade-banner />

<p class="text-sm text-slate-500 mb-6 -mt-4">{{ __('costs.variable.total_active', ['amount' => 'R$ '.number_format($total, 2, ',', '.')]) }}</p>

<div class="grid lg:grid-cols-2 gap-6 animate-fade-in">
    <x-ui.card>
        <x-slot:header>
            <h2 class="ui-section-title mb-0">{{ __('costs.variable.add_section') }}</h2>
        </x-slot:header>
        <form method="POST" action="{{ route('tenant.variable-costs.store') }}" class="space-y-4">
            @csrf
            <div><label class="ui-label">{{ __('costs.common.name') }}</label><input name="name" placeholder="{{ __('costs.variable.name_placeholder') }}" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('costs.common.monthly_amount') }}</label><input name="amount" type="number" step="0.01" placeholder="{{ __('costs.common.amount_placeholder') }}" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('costs.common.description') }}</label><input name="description" placeholder="{{ __('costs.common.optional') }}" class="ui-input"></div>
            <x-ui.button type="submit" class="w-full py-2.5">{{ __('costs.common.add_cost') }}</x-ui.button>
        </form>
    </x-ui.card>

    <div class="space-y-4">
        @forelse($variableCosts as $cost)
        <x-ui.card>
            <form method="POST" action="{{ route('tenant.variable-costs.update', $cost) }}" class="p-5 space-y-3">
                @csrf @method('PUT')
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1"><label class="ui-label">{{ __('costs.common.name') }}</label><input name="name" value="{{ $cost->name }}" class="ui-input"></div>
                    <div class="sm:w-32"><label class="ui-label">{{ __('costs.common.value') }}</label><input name="amount" type="number" step="0.01" value="{{ $cost->amount }}" class="ui-input"></div>
                </div>
                <div><label class="ui-label">{{ __('costs.common.description') }}</label><input name="description" value="{{ $cost->description }}" class="ui-input"></div>
                <div class="flex flex-wrap justify-between items-center gap-3 pt-1">
                    <label class="text-sm flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="is_active" value="1" @checked($cost->is_active) class="rounded border-slate-300 text-brand focus:ring-brand/30">
                        {{ __('costs.common.active') }}
                    </label>
                    <x-ui.button type="submit" variant="outline" class="py-2 px-4">{{ __('costs.common.save') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>
        <div class="text-right -mt-2 px-1">
            <x-ui.confirm-delete
                :action="route('tenant.variable-costs.destroy', $cost)"
                :message="__('costs.common.remove_confirm', ['name' => $cost->name])"
            >{{ __('costs.common.remove', ['name' => $cost->name]) }}</x-ui.confirm-delete>
        </div>
        @empty
        <x-ui.empty-state
            icon="variable-costs"
            :title="__('costs.variable.empty_title')"
            :description="__('costs.variable.empty_description')"
        />
        @endforelse
    </div>
</div>
@endsection
