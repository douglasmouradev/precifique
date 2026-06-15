@extends('layouts.tenant')
@section('title', __('sales.create.title'))
@section('breadcrumb') {{ __('sales.create.breadcrumb') }} @endsection

@section('content')
<x-ui.page-header :title="__('sales.create.page_title')" :subtitle="__('sales.create.subtitle')" />

<x-ui.card class="max-w-xl">
    <form method="POST" action="{{ route('tenant.sales.store') }}" class="space-y-5" x-data="{ price: 0, payment: 'pix' }">
        @csrf
        <div>
            <label class="ui-label">{{ __('sales.product') }}</label>
            <select name="product_id" required class="ui-input" @change="price = $event.target.selectedOptions[0]?.dataset.price || 0">
                <option value="">{{ __('sales.select') }}</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" data-price="{{ $p->selling_price ?? 0 }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="ui-label">{{ __('sales.quantity') }}</label><input type="number" name="quantity" value="1" min="1" required class="ui-input"></div>
            <div><label class="ui-label">{{ __('sales.unit_price') }}</label><input type="number" name="unit_price" step="0.01" x-model="price" required class="ui-input"></div>
        </div>
        <div>
            <label class="ui-label">{{ __('sales.payment_method') }}</label>
            <input type="hidden" name="payment_method" :value="payment">
            <div class="grid grid-cols-3 gap-2 mt-1">
                @foreach(\App\Enums\PaymentMethod::cases() as $method)
                <button
                    type="button"
                    @click="payment = '{{ $method->value }}'"
                    :aria-pressed="payment === '{{ $method->value }}'"
                    class="px-3 py-3 rounded-xl border text-sm font-semibold transition-colors"
                    :class="payment === '{{ $method->value }}'
                        ? 'bg-brand border-brand text-ink shadow-sm'
                        : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300'"
                >
                    {{ $method->label() }}
                </button>
                @endforeach
            </div>
        </div>
        <div><label class="ui-label">{{ __('sales.notes') }}</label><textarea name="notes" rows="2" class="ui-input"></textarea></div>
        <x-ui.button type="submit" class="w-full py-3">{{ __('sales.create.confirm') }}</x-ui.button>
    </form>
</x-ui.card>
@endsection
