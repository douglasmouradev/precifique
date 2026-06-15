<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <title>@yield('title') — Precifique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-ui.toast-container />
</head>
<body class="bg-paper font-sans text-ink min-h-screen" x-data="{ cookieAccepted: localStorage.getItem('precifique_cookies') === '1' }">
    <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ route('home') }}"><x-ui.logo variant="full" size="sm" /></a>
            <form method="POST" action="{{ route('tenant.logout') }}">@csrf
                <button type="submit" class="text-sm text-slate-500 hover:text-red-500">Sair</button>
            </form>
        </div>
    </header>

    @if(session('success'))
    <div class="max-w-3xl mx-auto px-4 pt-4 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-200" data-flash="success" role="status">{{ session('success') }}</div>
    @endif

    <main>@yield('content')</main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-flash]').forEach((el) => {
            const type = el.dataset.flash;
            const msg = el.textContent.trim();
            if (msg && window.toast?.[type]) window.toast[type](msg);
        });
    });
    </script>
    @stack('scripts')
</body>
</html>
