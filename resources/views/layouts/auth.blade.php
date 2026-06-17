<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-head-icons />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#00C896">
    <title>@yield('title', 'Precifique')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased min-h-screen bg-paper">
    <div class="min-h-screen flex">
        <aside class="ui-auth-brand-panel lg:w-[44%] xl:w-[42%]">
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-block transition-opacity hover:opacity-90">
                    <x-ui.logo variant="full" size="lg" dark />
                </a>
            </div>
            <div class="relative z-10 mt-auto space-y-6 max-w-md">
                <p class="text-brand text-xs font-semibold tracking-[0.2em] uppercase">{{ __('landing.hero_eyebrow') }}</p>
                <h1 class="font-display text-3xl xl:text-4xl font-bold leading-tight tracking-tight">
                    {{ __('landing.hero_headline') }}
                </h1>
                <p class="text-slate-400 leading-relaxed">{{ __('landing.hero_desc') }}</p>
                <ul class="space-y-3 text-sm text-slate-300">
                    @foreach(array_slice(__('landing.solution_features'), 0, 3) as $feature)
                    <li class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full bg-brand/20 text-brand flex items-center justify-center text-[10px] font-bold">✓</span>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
            </div>
            <p class="relative z-10 text-xs text-slate-500 mt-8">© {{ date('Y') }} Precifique</p>
        </aside>

        <main class="flex-1 flex flex-col min-h-screen relative gradient-mesh">
            <x-ui.locale-switcher class="absolute top-4 right-4 z-10" />
            <div class="flex-1 flex flex-col items-center justify-center px-4 py-12 sm:py-16">
                <a href="{{ route('home') }}" class="lg:hidden mb-8 transition-opacity hover:opacity-90">
                    <x-ui.logo variant="full" size="xl" />
                </a>

                <div class="w-full max-w-md ui-auth-card">
                    @yield('content')
                </div>

                @hasSection('footer')
                <div class="mt-6 text-center text-sm text-slate-500">
                    @yield('footer')
                </div>
                @endif

                <a href="{{ route('home') }}" class="mt-8 text-sm text-slate-400 hover:text-brand transition-colors">{{ __('auth.back_to_site') }}</a>
            </div>
        </main>
    </div>
</body>
</html>
