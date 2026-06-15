<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página não encontrada — Precifique</title>
    <x-head-icons />
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper flex items-center justify-center p-6 font-sans text-ink">
    <div class="text-center max-w-md">
        <div class="flex justify-center mb-6">
            <x-ui.logo variant="full" size="lg" />
        </div>
        <p class="text-6xl font-display font-bold text-brand">404</p>
        <h1 class="font-display text-2xl font-bold mt-4">Página não encontrada</h1>
        <p class="text-slate-500 mt-2 text-sm">O endereço pode ter mudado ou não existe mais.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="ui-btn-secondary px-6 py-3">Ir para o site</a>
            <a href="{{ route('tenant.login') }}" class="ui-btn-outline px-6 py-3">Entrar</a>
        </div>
    </div>
</body>
</html>
