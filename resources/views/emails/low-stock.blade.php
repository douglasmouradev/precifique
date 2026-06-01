<x-mail::message>
# Estoque baixo — {{ $tenant->name }}

Os seguintes produtos estão no limite mínimo:

@foreach($products as $product)
- **{{ $product->name }}**: {{ $product->stock_quantity }} un. (mín: {{ $product->min_stock_alert }})
@endforeach

<x-mail::button :url="route('tenant.stock.index')">
Ver estoque
</x-mail::button>
</x-mail::message>
