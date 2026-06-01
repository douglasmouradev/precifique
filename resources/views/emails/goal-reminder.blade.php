<x-mail::message>
# Lembrete de meta — {{ $tenant->name }}

Você está com **{{ number_format($progress, 0) }}%** da meta de R$ {{ number_format($goal->goal_amount, 2, ',', '.') }}.

Faturamento atual: **R$ {{ number_format($revenue, 2, ',', '.') }}**

<x-mail::button :url="route('tenant.dashboard')">
Ver dashboard
</x-mail::button>
</x-mail::message>
