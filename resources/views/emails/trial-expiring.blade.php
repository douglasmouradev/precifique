<x-mail::message>
# Olá, {{ $tenant->name }}

Seu período de teste Premium do Precifique termina em **{{ $tenant->trial_ends_at?->format('d/m/Y') }}**.

Para continuar com produtos ilimitados, IA e relatórios:

<x-mail::button :url="route('tenant.billing.upgrade')">
Fazer upgrade
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
