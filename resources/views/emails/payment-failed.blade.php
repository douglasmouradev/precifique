<x-mail::message>
# Falha no pagamento

Olá {{ $tenant->name }},

Não conseguimos processar o pagamento da sua assinatura Premium.

<x-mail::button :url="route('tenant.billing.portal')">
Atualizar pagamento
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
