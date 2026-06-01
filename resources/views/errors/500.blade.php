<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erro interno — Precifique</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper flex items-center justify-center p-6 font-sans text-ink">
    <div class="text-center max-w-md">
        <p class="text-6xl font-display font-bold text-slate-300">500</p>
        <h1 class="font-display text-2xl font-bold mt-4">Algo deu errado</h1>
        <p class="text-slate-500 mt-2 text-sm">Nossa equipe foi notificada. Tente novamente em instantes.</p>
        <a href="{{ route('home') }}" class="ui-btn-primary inline-flex mt-8 px-6 py-3">Voltar ao início</a>
    </div>
</body>
</html>
