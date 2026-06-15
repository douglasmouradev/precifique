<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-head-icons />
    <title>@yield('title', 'Precifique')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased bg-paper min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-16 sm:py-20">
        <a href="{{ route('home') }}" class="mb-8 transition-opacity hover:opacity-90">
            <x-ui.logo variant="full" size="xl" />
        </a>

        <div class="w-full max-w-md ui-card p-8 sm:p-10">
            @yield('content')
        </div>

        @hasSection('footer')
        <div class="mt-6 text-center text-sm text-slate-500">
            @yield('footer')
        </div>
        @endif

        <a href="{{ route('home') }}" class="mt-8 text-sm text-slate-400 hover:text-brand transition-colors">← Voltar ao site</a>
    </div>
</body>
</html>
