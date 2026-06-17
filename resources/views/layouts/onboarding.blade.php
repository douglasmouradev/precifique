<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-head-icons />
    <title>@yield('title', __('onboarding.layout.title'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased bg-paper min-h-screen relative gradient-mesh">
    <x-ui.skip-link />
    <x-ui.locale-switcher class="absolute top-4 right-4 z-20" />
    @php
        $step = $step ?? 0;
        $steps = array_values(__('onboarding.layout.steps'));
    @endphp
    <div class="min-h-screen flex flex-col">
        <header class="border-b border-slate-200/70 bg-white/90 backdrop-blur-md sticky top-0 z-10 shadow-sm">
            <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="{{ route('home') }}"><x-ui.logo variant="full" size="md" /></a>
                @if($step > 0)
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ __('onboarding.layout.step', ['current' => $step, 'total' => count($steps)]) }}</span>
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

        <main id="main-content" class="flex-1 flex flex-col items-center justify-center px-4 py-12 sm:py-16">
            <div class="w-full max-w-2xl animate-fade-in">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
