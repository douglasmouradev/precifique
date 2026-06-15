@extends('layouts.tenant')
@section('title', __('sales.edit.title'))
@section('breadcrumb') {{ __('sales.edit.breadcrumb') }} @endsection

@section('content')
<x-ui.page-header :title="__('sales.edit.page_title')" :subtitle="$sale->product?->name" />

<x-ui.card class="max-w-xl">
    <form method="POST" action="{{ route('tenant.sales.update', $sale) }}" class="space-y-5" x-data="{ payment: '{{ $sale->payment_method->value ?? $sale->payment_method }}' }">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="ui-label">{{ __('sales.quantity') }}</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', $sale->quantity) }}" required class="ui-input">
            </div>
            <div>
                <label class="ui-label">{{ __('sales.unit_price') }}</label>
                <input type="number" name="unit_price" step="0.01" value="{{ $sale->unit_price }}" required class="ui-input">
            </div>
        </div>
        <div>
            <label class="ui-label">{{ __('sales.sold_at') }}</label>
            <input type="datetime-local" name="sold_at" value="{{ $sale->sold_at?->format('Y-m-d\TH:i') }}" required class="ui-input">
        </div>
        <div>
            <label class="ui-label">{{ __('sales.payment_method') }}</label>
            <input type="hidden" name="payment_method" :value="payment">
            <div class="grid grid-cols-3 gap-2 mt-1">
                @foreach(\App\Enums\PaymentMethod::cases() as $method)
                <button type="button" @click="payment = '{{ $method->value }}'"
                    :aria-pressed="payment === '{{ $method->value }}'"
                    class="px-3 py-3 rounded-xl border text-sm font-semibold transition-colors"
                    :class="payment === '{{ $method->value }}' ? 'bg-brand border-brand text-ink shadow-sm' : 'bg-white border-slate-200 text-slate-600'">
                    {{ $method->label() }}
                </button>
                @endforeach
            </div>
        </div>
        <div>
            <label class="ui-label">{{ __('sales.notes') }}</label>
            <textarea name="notes" rows="3" class="ui-input">{{ $sale->notes }}</textarea>
        </div>
        <div class="flex gap-3">
            <x-ui.button type="submit">{{ __('app.actions.save') }}</x-ui.button>
            <x-ui.button variant="ghost" :href="route('tenant.sales.index')">{{ __('sales.cancel') }}</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection
