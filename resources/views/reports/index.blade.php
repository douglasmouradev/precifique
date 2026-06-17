@extends('layouts.tenant')
@section('title', __('reports.title'))
@section('breadcrumb') {{ __('reports.title') }} @endsection

@section('content')
<x-ui.page-header :title="__('reports.title')" :subtitle="__('reports.subtitle')" />

@if($summary)
<x-ui.card class="max-w-xl mb-6">
    <h2 class="ui-section-title mb-4">{{ __('reports.preview_title', ['period' => sprintf('%02d/%d', $month, $year)]) }}</h2>
    @if($summary['has_data'])
    <dl class="grid sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-slate-500">{{ __('reports.preview_revenue') }}</dt><dd class="font-semibold text-lg">R$ {{ number_format($summary['revenue'], 2, ',', '.') }}</dd></div>
        <div><dt class="text-slate-500">{{ __('reports.preview_sales') }}</dt><dd class="font-semibold text-lg">{{ $summary['sales_count'] }}</dd></div>
        <div><dt class="text-slate-500">{{ __('reports.preview_fixed_costs') }}</dt><dd class="font-medium">R$ {{ number_format($summary['fixed_costs'], 2, ',', '.') }}</dd></div>
        <div><dt class="text-slate-500">{{ __('reports.preview_balance') }}</dt><dd class="font-medium {{ $summary['estimated_balance'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">R$ {{ number_format($summary['estimated_balance'], 2, ',', '.') }}</dd></div>
    </dl>
    @else
    <x-ui.empty-state icon="reports" :title="__('reports.empty_period_title')" :description="__('reports.empty_period_desc')" class="border-0 shadow-none py-4" />
    @endif
</x-ui.card>
@endif

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
