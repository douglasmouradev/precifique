<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-head-icons />
        <title>{{ $title ?? 'Admin' }} — Precifique</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased bg-paper">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4 pb-8 gradient-mesh">
            <a href="{{ route('home') }}" class="transition-opacity hover:opacity-90 mb-2">
                <x-ui.logo variant="full" size="xl" />
            </a>
            <p class="text-sm text-slate-500 mb-6">Painel administrativo</p>

            <div class="w-full sm:max-w-md ui-card p-6 sm:p-8">
                {{ $slot }}
            </div>

            <p class="mt-6 text-sm text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-brand transition-colors">← Voltar ao site</a>
            </p>
        </div>
    </body>
</html>
