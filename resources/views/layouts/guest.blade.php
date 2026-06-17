<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <x-head-icons />
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#00C896">
        <title>{{ $title ?? 'Admin' }} — Precifique</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased min-h-screen bg-paper">
        <div class="min-h-screen flex">
            <aside class="ui-auth-brand-panel lg:w-[40%] xl:w-[38%]">
                <div class="relative z-10">
                    <a href="{{ route('home') }}" class="inline-block transition-opacity hover:opacity-90">
                        <x-ui.logo variant="full" size="lg" dark />
                    </a>
                    <p class="mt-3 text-sm text-slate-400">{{ __('auth.admin_login.panel_subtitle') }}</p>
                </div>
                <div class="relative z-10 mt-auto">
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand mb-3">{{ __('auth.two_factor.admin_required') }}</p>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-sm">{{ __('landing.hero_desc') }}</p>
                </div>
            </aside>
            <main class="flex-1 flex flex-col items-center justify-center px-4 py-12 gradient-mesh">
                <a href="{{ route('home') }}" class="lg:hidden mb-6 transition-opacity hover:opacity-90">
                    <x-ui.logo variant="full" size="xl" />
                </a>
                <p class="lg:hidden text-sm text-slate-500 mb-6">{{ __('auth.admin_login.panel_subtitle') }}</p>

                <div class="w-full max-w-md ui-auth-card">
                    {{ $slot }}
                </div>

                <p class="mt-8 text-sm text-slate-400">
                    <a href="{{ route('home') }}" class="hover:text-brand transition-colors">{{ __('auth.back_to_site') }}</a>
                </p>
            </main>
        </div>
    </body>
</html>
