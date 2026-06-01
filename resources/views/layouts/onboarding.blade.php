<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <title>@yield('title', 'Configuração — Precifique')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased bg-paper min-h-screen">
    @php
        $step = $step ?? 0;
        $steps = ['Nicho', 'Modo', 'Plano', 'Setup'];
    @endphp
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-slate-200/70 bg-white/80 backdrop-blur-sm sticky top-0 z-10">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ route('home') }}"><x-ui.logo variant="full" size="md" /></a>
                @if($step > 0)
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Passo {{ $step }} de {{ count($steps) }}</span>
                @endif
            </div>
            @if($step > 0)
            <div class="max-w-2xl mx-auto px-4 pb-4">
                <div class="flex gap-1">
                    @foreach($steps as $i => $label)
                    <div class="flex-1 h-1 rounded-full {{ ($i + 1) <= $step ? 'bg-brand' : 'bg-slate-200' }}"></div>
                    @endforeach
                </div>
            </div>
            @endif
        </header>

        <main class="flex-1 flex flex-col items-center justify-center px-4 py-12 sm:py-16">
            <div class="w-full max-w-2xl">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
