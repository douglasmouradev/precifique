<x-mail::message>
# Bem-vindo ao Precifique, {{ $tenant->name }}!

Sua conta foi criada pelo administrador.

**E-mail:** {{ $tenant->email }}

Para definir sua senha, use o link abaixo (válido por tempo limitado):

<x-mail::button :url="$resetUrl">
Definir senha e entrar
</x-mail::button>

Se você não solicitou esta conta, ignore este e-mail.
</x-mail::message>
