<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-head-icons />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#00C896">
    <title>@yield('title', __('admin.nav.dashboard')) — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-analytics />
    <x-ui.toast-container />
    @stack('head')
</head>
@php
    $adminLinks = [
        ['route' => 'admin.dashboard', 'label' => __('admin.nav.dashboard'), 'icon' => 'dashboard', 'match' => 'admin.dashboard'],
        ['route' => 'admin.tenants.index', 'label' => __('admin.nav.tenants'), 'icon' => 'products', 'match' => 'admin.tenants.*'],
        ['route' => 'admin.plans.index', 'label' => __('admin.nav.plans'), 'icon' => 'revenue', 'match' => 'admin.plans.*'],
        ['route' => 'admin.logs.index', 'label' => __('admin.nav.logs'), 'icon' => 'reports', 'match' => 'admin.logs.*'],
        ['route' => 'admin.failed-jobs.index', 'label' => __('admin.nav.failed_jobs'), 'icon' => 'edit', 'match' => 'admin.failed-jobs.*'],
        ['route' => 'admin.lgpd', 'label' => __('admin.nav.lgpd'), 'icon' => 'edit', 'match' => 'admin.lgpd'],
    ];
@endphp
<body class="bg-paper font-sans text-ink min-h-screen">
    <x-ui.skip-link />

    <input
        type="checkbox"
        id="admin-sidebar-check"
        class="sr-only"
        aria-hidden="true"
        tabindex="-1"
    >

    <label
        for="admin-sidebar-check"
        id="admin-sidebar-overlay"
        class="fixed inset-0 z-[74] bg-ink/60 backdrop-blur-sm lg:hidden opacity-0 pointer-events-none transition-opacity duration-300 cursor-pointer"
        aria-hidden="true"
    ></label>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-[75] w-[16.5rem] max-w-[85vw] ui-sidebar-premium text-white flex flex-col shadow-sidebar transition-transform duration-300 ease-out -translate-x-full lg:translate-x-0 pointer-events-none lg:pointer-events-auto" aria-hidden="true">
        <div class="px-4 py-4 border-b border-white/[0.06] flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="flex-1 min-w-0" data-admin-sidebar-close>
                <x-ui.logo variant="sidebar" class="w-full" />
            </a>
            <label for="admin-sidebar-check" id="admin-sidebar-close" class="lg:hidden shrink-0 p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 cursor-pointer touch-manipulation" role="button" aria-label="{{ __('messages.sidebar.close_menu') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </label>
        </div>
        <nav class="flex-1 p-3 overflow-y-auto space-y-0.5">
            @foreach($adminLinks as $link)
            <a href="{{ route($link['route']) }}" class="{{ request()->routeIs($link['match']) ? 'ui-sidebar-link-active' : 'ui-sidebar-link' }}" data-admin-sidebar-close>
                <x-ui.nav-icon :name="$link['icon']" class="w-[1.125rem] h-[1.125rem] shrink-0 opacity-80" />
                {{ $link['label'] }}
            </a>
            @endforeach
        </nav>
        <div class="p-4 border-t border-white/[0.06] space-y-2">
            <div class="rounded-xl bg-white/[0.04] ring-1 ring-white/[0.06] p-3">
                <p class="text-xs font-medium text-slate-300 truncate">{{ Auth::user()?->name }}</p>
                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ Auth::user()?->email }}</p>
            </div>
            <div class="flex flex-col gap-1 text-xs">
                <a href="{{ route('profile.two-factor') }}" class="ui-sidebar-link justify-center py-2">{{ __('app.account.two_factor') }}</a>
                <a href="{{ route('home') }}" class="ui-sidebar-link justify-center py-2">{{ __('admin.nav.site') }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="ui-sidebar-link w-full justify-center py-2 text-red-400/90 hover:text-red-300">{{ __('admin.nav.logout') }}</button>
                </form>
            </div>
        </div>
    </aside>

    <div id="admin-main" class="min-h-screen lg:pl-[16.5rem] flex flex-col">
        <header class="sticky top-0 z-[70] ui-glass-header">
            <div class="flex items-center justify-between gap-4 px-4 md:px-8 h-14">
                <div class="flex items-center gap-3 min-w-0">
                    <label
                        for="admin-sidebar-check"
                        id="admin-sidebar-toggle"
                        class="lg:hidden relative z-[80] p-2.5 min-w-[2.75rem] min-h-[2.75rem] rounded-xl border border-slate-200 bg-white hover:bg-slate-50 shadow-sm touch-manipulation cursor-pointer inline-flex items-center justify-center"
                        role="button"
                        aria-controls="admin-sidebar"
                        aria-label="{{ __('messages.sidebar.open_menu') }}"
                    >
                        <svg data-icon="open" class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </label>
                    @hasSection('breadcrumb')
                    <p class="text-sm text-slate-500 truncate">@yield('breadcrumb')</p>
                    @endif
                </div>
                <x-ui.locale-switcher />
            </div>
        </header>

        @if(session('success'))
        <div class="mx-4 md:mx-8 mt-4 ui-alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
        <div class="mx-4 md:mx-8 mt-4 ui-alert-warning" role="status">{{ session('warning') }}</div>
        @endif
        @if(session('recovery_codes'))
        <div class="mx-4 md:mx-8 mt-4 rounded-xl bg-slate-900 text-white text-sm px-4 py-4" role="status">
            <p class="font-semibold mb-2">{{ __('auth.two_factor.recovery_saved_title') }}</p>
            <p class="text-slate-300 text-xs mb-3">{{ __('auth.two_factor.recovery_saved_hint') }}</p>
            <ul class="grid sm:grid-cols-2 gap-2 font-mono text-xs">
                @foreach(session('recovery_codes') as $code)
                <li class="bg-white/10 rounded px-2 py-1">{{ $code }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <main id="main-content" class="flex-1 px-4 md:px-8 py-6 md:py-8 app-shell-bg animate-fade-in max-w-7xl w-full mx-auto">
            @isset($header)
            <div class="mb-6">{{ $header }}</div>
            @endisset
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
