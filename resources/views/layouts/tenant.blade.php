<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <x-head-icons />
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#00C896">
    <title>@yield('title', 'App') — Precifique</title>
    <link rel="preconnect" href="{{ config('app.url') }}" crossorigin>
    <x-analytics />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-ui.toast-container />
    @php $cspNonce = request()->attributes->get('csp_nonce'); @endphp
    <style @if(is_string($cspNonce) && $cspNonce !== '') nonce="{{ $cspNonce }}" @endif>
        html.cookies-accepted #tenant-cookie-banner { display: none !important; }
    </style>
    <script @if(is_string($cspNonce) && $cspNonce !== '') nonce="{{ $cspNonce }}" @endif>
        try {
            if (localStorage.getItem('precifique_cookies') === '1') {
                document.documentElement.classList.add('cookies-accepted');
            }
        } catch (e) {}
        document.addEventListener('DOMContentLoaded', function () {
            var acceptBtn = document.getElementById('tenant-cookie-accept');
            var banner = document.getElementById('tenant-cookie-banner');
            if (!acceptBtn || !banner) return;
            acceptBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                try { localStorage.setItem('precifique_cookies', '1'); } catch (err) {}
                document.documentElement.classList.add('cookies-accepted');
                banner.remove();
            });
        });
        (function () {
            function bindTenantSidebarFallback() {
                if (document.documentElement.dataset.tenantSidebarInit === '1') return;
                var sidebar = document.getElementById('tenant-sidebar');
                var overlay = document.getElementById('tenant-sidebar-overlay');
                var toggle = document.getElementById('tenant-sidebar-toggle');
                if (!sidebar || !toggle || toggle.dataset.fallbackBound === '1') return;
                toggle.dataset.fallbackBound = '1';
                var open = false;
                function apply() {
                    sidebar.classList.toggle('-translate-x-full', !open);
                    sidebar.classList.toggle('pointer-events-none', !open);
                    sidebar.classList.toggle('is-open', open);
                    sidebar.setAttribute('aria-hidden', open ? 'false' : 'true');
                    if (overlay) {
                        overlay.classList.toggle('hidden', !open);
                        overlay.setAttribute('aria-hidden', open ? 'false' : 'true');
                    }
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    var iconOpen = toggle.querySelector('[data-icon="open"]');
                    var iconClose = toggle.querySelector('[data-icon="close"]');
                    if (iconOpen) iconOpen.classList.toggle('hidden', open);
                    if (iconClose) iconClose.classList.toggle('hidden', !open);
                    if (window.matchMedia('(max-width: 1023px)').matches) {
                        document.body.style.overflow = open ? 'hidden' : '';
                    }
                }
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    open = !open;
                    apply();
                });
                overlay && overlay.addEventListener('click', function () {
                    open = false;
                    apply();
                });
                document.getElementById('tenant-sidebar-close')?.addEventListener('click', function (e) {
                    e.preventDefault();
                    open = false;
                    apply();
                });
            }
            function scheduleTenantSidebarFallback() {
                window.setTimeout(bindTenantSidebarFallback, 0);
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', scheduleTenantSidebarFallback);
            } else {
                scheduleTenantSidebarFallback();
            }
        })();
    </script>
    @stack('head')
</head>
@php
    $tenant = auth('tenant')->user();
    $navGroups = [
        [
            'label' => __('app.nav.group_operation'),
            'items' => [
                ['route' => 'tenant.dashboard', 'label' => __('app.nav.dashboard'), 'icon' => 'dashboard', 'match' => 'tenant.dashboard'],
                ['route' => 'tenant.products.index', 'label' => __('app.nav.products'), 'icon' => 'products', 'match' => 'tenant.products.*'],
                ['route' => 'tenant.sales.index', 'label' => __('app.nav.sales'), 'icon' => 'sales', 'match' => 'tenant.sales.*'],
                ['route' => 'tenant.stock.index', 'label' => __('app.nav.stock'), 'icon' => 'stock', 'match' => 'tenant.stock.*'],
            ],
        ],
        [
            'label' => __('app.nav.group_finance'),
            'items' => [
                ['route' => 'tenant.fixed-costs.index', 'label' => __('app.nav.fixed_costs'), 'icon' => 'fixed-costs', 'match' => 'tenant.fixed-costs.*'],
                ['route' => 'tenant.variable-costs.index', 'label' => __('app.nav.variable_costs'), 'icon' => 'variable-costs', 'match' => 'tenant.variable-costs.*'],
                ['route' => 'tenant.goals.edit', 'label' => __('app.nav.goals'), 'icon' => 'goals', 'match' => 'tenant.goals.*'],
            ],
        ],
        [
            'label' => __('app.nav.group_account'),
            'items' => array_values(array_filter([
                $tenant?->isPremium()
                    ? ['route' => 'tenant.reports.index', 'label' => __('app.nav.reports'), 'icon' => 'reports', 'match' => 'tenant.reports.*']
                    : null,
                ['route' => 'tenant.account.index', 'label' => __('app.nav.account'), 'icon' => 'edit', 'match' => 'tenant.account.*'],
            ])),
        ],
    ];
@endphp
<body class="bg-paper font-sans text-ink min-h-screen" data-analytics-page="@yield('analytics_page', '')">
    <x-ui.skip-link />

    <div
        id="tenant-sidebar-overlay"
        class="fixed inset-0 z-40 bg-ink/60 backdrop-blur-sm lg:bg-ink/20 lg:backdrop-blur-none cursor-pointer hidden"
        aria-hidden="true"
    ></div>

    <aside
        id="tenant-sidebar"
        class="fixed inset-y-0 left-0 z-[75] w-[16.5rem] max-w-[85vw] ui-sidebar-premium text-white flex flex-col shadow-sidebar transition-transform duration-300 ease-out -translate-x-full pointer-events-none"
        aria-hidden="true"
    >
        <div class="px-4 py-4 border-b border-white/[0.06] flex items-center gap-2">
            <a
                href="{{ route('tenant.dashboard') }}"
                class="flex-1 min-w-0 flex items-center group"
                data-sidebar-close
                aria-label="Precifique — ir ao dashboard"
            >
                <x-ui.logo
                    variant="sidebar"
                    class="w-full transition-opacity group-hover:opacity-90"
                />
            </a>
            <button
                type="button"
                id="tenant-sidebar-close"
                class="shrink-0 p-2.5 min-w-[2.75rem] min-h-[2.75rem] rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors -mr-1 touch-manipulation"
                aria-label="{{ __('messages.sidebar.close_menu') }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-3 overflow-y-auto">
            @foreach($navGroups as $group)
            <p class="ui-sidebar-group-label">{{ $group['label'] }}</p>
            <div class="space-y-0.5 mb-2">
                @foreach($group['items'] as $item)
                <a href="{{ route($item['route']) }}"
                   class="{{ request()->routeIs($item['match']) ? 'ui-sidebar-link-active' : 'ui-sidebar-link' }}"
                   data-sidebar-close>
                    <x-ui.nav-icon :name="$item['icon']" class="w-[1.125rem] h-[1.125rem] shrink-0 opacity-80" />
                    {{ $item['label'] }}
                </a>
                @endforeach
            </div>
            @endforeach
        </nav>

        <div class="p-4 border-t border-white/[0.06] space-y-3">
            <div class="rounded-xl bg-white/[0.04] ring-1 ring-white/[0.06] p-3">
                <p class="text-xs font-medium text-slate-300 truncate">{{ $tenant?->name }}</p>
                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $tenant?->email }}</p>
                <div class="mt-2.5">
                    @if($tenant?->isPremium())
                    <span class="ui-badge-premium">Premium</span>
                    @else
                    <a href="{{ route('tenant.billing.upgrade') }}" class="ui-badge-brand hover:bg-brand/20 transition-colors">{{ __('app.account.upgrade') }}</a>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 text-xs">
                <a href="{{ route('tenant.lgpd.portal') }}" class="ui-sidebar-link flex-1 justify-center py-2">{{ __('app.nav.privacy') }}</a>
                <form method="POST" action="{{ route('tenant.logout') }}" class="flex-1">@csrf
                    <button type="submit" class="ui-sidebar-link w-full justify-center py-2 text-red-400/90 hover:text-red-300">{{ __('app.nav.logout') }}</button>
                </form>
            </div>
        </div>
    </aside>

    <div
        id="tenant-main"
        class="min-h-screen flex flex-col transition-[padding] duration-300 ease-out"
    >
        <header class="sticky top-0 z-[70] ui-glass-header">
            <div class="flex items-center justify-between gap-4 px-4 md:px-8 h-14">
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        type="button"
                        id="tenant-sidebar-toggle"
                        data-label-open="{{ __('messages.sidebar.open_menu') }}"
                        data-label-close="{{ __('messages.sidebar.close_menu') }}"
                        class="relative z-[1] p-2.5 min-w-[2.75rem] min-h-[2.75rem] rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-colors touch-manipulation cursor-pointer"
                        aria-expanded="false"
                        aria-controls="tenant-sidebar"
                        aria-label="{{ __('messages.sidebar.open_menu') }}"
                    >
                        <span class="sr-only">{{ __('messages.sidebar.open_menu') }}</span>
                        <svg data-icon="open" class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg data-icon="close" class="w-5 h-5 text-ink hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @hasSection('breadcrumb')
                    <div class="text-sm text-slate-500 truncate">@yield('breadcrumb')</div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.locale-switcher />
                    <x-ui.notification-bell />
                    @if($tenant?->isPremium())
                    <button type="button" data-ai-open class="ui-btn-outline px-3 py-2 text-xs hidden sm:inline-flex">{{ __('messages.sidebar.assistant') }}</button>
                    @endif
                    @yield('header-actions')
                </div>
            </div>
        </header>

        @if(session('success'))
        <div class="mx-4 md:mx-8 mt-4 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-200 px-4 py-3" data-flash="success" role="status" aria-live="polite">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
        <div class="mx-4 md:mx-8 mt-4 rounded-lg bg-amber-50 text-amber-800 text-sm border border-amber-200 px-4 py-3" data-flash="warning" role="status" aria-live="polite">{{ session('warning') }}</div>
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
        @if(session()->has('impersonating_from_admin'))
        <div class="sticky top-14 z-[65] bg-amber-400 text-amber-950 text-sm font-semibold px-4 md:px-8 py-2.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 shadow-md border-b border-amber-500/40" role="alert" aria-live="assertive">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                {{ __('messages.support_mode') }}
            </span>
            <form method="POST" action="{{ route('tenant.impersonate.stop') }}">@csrf
                <button type="submit" class="text-sm font-bold underline underline-offset-2 hover:text-amber-900">{{ __('messages.support_exit') }}</button>
            </form>
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 md:mx-8 mt-4 rounded-lg bg-red-50 text-red-800 text-sm border border-red-200 px-4 py-3" data-flash="error" role="alert" aria-live="assertive">{{ session('error') }}</div>
        @endif

        <main id="main-content" class="flex-1 px-4 md:px-8 py-6 md:py-8 pb-24 lg:pb-8 app-shell-bg animate-fade-in">
            @isset($setupProgress)
            <x-ui.setup-progress :progress="$setupProgress" />
            @endisset
            @yield('content')
        </main>
    </div>

    <nav class="ui-bottom-nav" aria-label="{{ __('app.nav.menu') }}">
        @foreach([
            ['route' => 'tenant.dashboard', 'label' => __('app.nav.dashboard'), 'icon' => 'dashboard', 'match' => 'tenant.dashboard'],
            ['route' => 'tenant.sales.create', 'label' => '+', 'icon' => 'sales', 'match' => 'tenant.sales.create', 'accent' => true],
            ['route' => 'tenant.products.index', 'label' => __('app.nav.products'), 'icon' => 'products', 'match' => 'tenant.products.*'],
            ['route' => 'tenant.sales.index', 'label' => __('app.nav.sales'), 'icon' => 'sales', 'match' => 'tenant.sales.index'],
            ['route' => 'tenant.menu', 'label' => __('app.nav.menu'), 'icon' => 'menu', 'match' => 'tenant.menu'],
        ] as $tab)
        <a href="{{ route($tab['route']) }}"
           class="{{ request()->routeIs($tab['match']) ? 'ui-bottom-nav-link-active' : 'ui-bottom-nav-link' }} {{ ($tab['accent'] ?? false) ? '!text-brand-dark font-bold text-sm' : '' }}"
           @if(request()->routeIs($tab['match'])) aria-current="page" @endif>
            <x-ui.nav-icon :name="$tab['icon']" class="w-5 h-5" />
            {{ $tab['label'] }}
        </a>
        @endforeach
    </nav>

    @if($tenant?->isPremium())
    <div
        id="tenant-ai-assistant"
        class="hidden fixed bottom-20 right-4 lg:bottom-4 z-50 w-[calc(100%-2rem)] max-w-md"
        role="dialog"
        aria-labelledby="tenant-ai-title"
        aria-hidden="true"
        data-chat-url="{{ route('tenant.ai.chat') }}"
        data-csrf="{{ csrf_token() }}"
        data-no-answer="{{ __('messages.ai.no_answer') }}"
        data-error="{{ __('messages.ai.error') }}"
    >
        <div class="ui-card shadow-card-hover overflow-hidden">
            <div class="bg-ink text-white px-4 py-3 flex justify-between items-center">
                <div>
                    <p id="tenant-ai-title" class="font-semibold text-sm">{{ __('messages.ai.title') }}</p>
                    <p class="text-[11px] text-slate-400">{{ __('messages.ai.subtitle') }}</p>
                </div>
                <button type="button" data-ai-close class="text-slate-400 hover:text-white p-1 touch-manipulation" aria-label="{{ __('messages.ai.close') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div data-ai-answer class="hidden p-4 min-h-[100px] max-h-52 overflow-y-auto text-sm text-slate-600 leading-relaxed"></div>
            <div data-ai-loading class="hidden px-4 pb-2 text-xs text-slate-500 flex items-center gap-2">
                <span class="w-3 h-3 border-2 border-brand/30 border-t-brand rounded-full animate-spin"></span>
                {{ __('messages.ai.loading') }}
            </div>
            <div class="p-3 border-t border-slate-100 flex gap-2 bg-slate-50/50">
                <input
                    data-ai-question
                    type="text"
                    placeholder="{{ __('messages.ai.placeholder') }}"
                    class="ui-input flex-1 py-2 text-sm"
                >
                <button type="button" data-ai-send class="ui-btn-primary px-4 py-2 text-sm touch-manipulation">
                    <span data-ai-send-label>{{ __('messages.ai.send') }}</span>
                    <span data-ai-send-busy class="hidden">…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <div
        id="tenant-cookie-banner"
        class="fixed bottom-16 lg:bottom-0 inset-x-0 lg:left-0 z-[65] bg-ink text-white p-4 shadow-2xl transition-[left] duration-300 pointer-events-auto"
        role="dialog"
        aria-label="{{ __('messages.sidebar.cookie_message') }}"
    >
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm">{{ __('messages.sidebar.cookie_message') }} <a href="{{ route('privacy') }}" class="text-brand underline">{{ __('app.nav.privacy') }}</a></p>
            <button
                type="button"
                id="tenant-cookie-accept"
                class="bg-brand hover:bg-brand-dark px-6 py-3 min-h-[2.75rem] rounded-lg font-semibold text-ink shrink-0 touch-manipulation"
            >{{ __('messages.sidebar.cookie_accept') }}</button>
        </div>
    </div>

    @stack('scripts')
    @php $footerCspNonce = request()->attributes->get('csp_nonce'); @endphp
    <script @if(is_string($footerCspNonce) && $footerCspNonce !== '') nonce="{{ $footerCspNonce }}" @endif>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('{{ asset('sw.js') }}', { scope: '/' }).catch(() => {});
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-flash]').forEach((el) => {
            const type = el.dataset.flash;
            const msg = el.textContent.trim();
            if (msg && window.toast?.[type]) window.toast[type](msg);
        });
    });
    </script>
</body>
</html>
