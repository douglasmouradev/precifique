@extends('layouts.tenant')
@section('title', __('reports.title'))
@section('breadcrumb') {{ __('reports.title') }} @endsection

@section('content')
<x-ui.page-header :title="__('reports.title')" :subtitle="__('reports.subtitle')" />

<x-ui.card class="max-w-xl">
    <p class="text-sm text-slate-500 mb-6">{{ __('reports.includes') }}</p>

    <form method="GET" action="{{ route('tenant.reports.monthly') }}" class="space-y-4">
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="month" class="ui-label">{{ __('reports.month') }}</label>
                <select id="month" name="month" class="ui-input">
                    @foreach(__('sales.months') as $num => $label)
                    <option value="{{ $num }}" @selected((int) old('month', $month) === (int) $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="year" class="ui-label">{{ __('reports.year') }}</label>
                <input id="year" type="number" name="year" min="2020" max="2100" value="{{ old('year', $year) }}" class="ui-input" required>
            </div>
        </div>
        <x-ui.button type="submit" class="w-full sm:w-auto">{{ __('reports.download') }}</x-ui.button>
    </form>
</x-ui.card>
@endsection
