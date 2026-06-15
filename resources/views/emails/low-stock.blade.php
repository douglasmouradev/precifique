<x-mail::message>
# {{ __('mail.low_stock.heading', ['name' => $tenant->name]) }}

{{ __('mail.low_stock.intro') }}

@foreach($products as $product)
- {{ __('mail.low_stock.item', [
    'name' => $product->name,
    'quantity' => $product->stock_quantity,
    'min' => $product->min_stock_alert,
]) }}
@endforeach

<x-mail::button :url="route('tenant.stock.index')">
{{ __('mail.low_stock.button') }}
</x-mail::button>
</x-mail::message>
