<x-mail::message>
# PIX Premium expirando

Olá {{ $tenant->name }},

Sua assinatura via PIX expira em **{{ $subscription->ends_at?->format('d/m/Y') }}**.

<x-mail::button :url="route('tenant.billing.upgrade')">
Renovar Premium
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
