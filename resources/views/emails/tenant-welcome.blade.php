<x-mail::message>
# Bem-vindo ao Precifique, {{ $tenant->name }}!

Sua conta foi criada.

**E-mail:** {{ $tenant->email }}
**Senha temporária:** {{ $plainPassword }}

<x-mail::button :url="route('tenant.login')">
Acessar o sistema
</x-mail::button>

Altere sua senha após o primeiro acesso.
</x-mail::message>
