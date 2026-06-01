<x-mail::message>
# Relatório mensal — Precifique

Olá, {{ $tenant->name }}!

Seu relatório mensal está em anexo (Excel).

<x-mail::button :url="route('tenant.dashboard')">
Acessar Precifique
</x-mail::button>
</x-mail::message>
