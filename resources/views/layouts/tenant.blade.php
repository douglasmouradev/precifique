<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#00C896">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>@yield('title', 'App') — Precifique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-ui.toast-container />
    @stack('head')
</head>
@php
    $tenant = auth('tenant')->user();
    $nav = [
        ['route' => 'tenant.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'match' => 'tenant.dashboard'],
        ['route' => 'tenant.products.index', 'label' => 'Produtos', 'icon' => 'products', 'match' => 'tenant.products.*'],
        ['route' => 'tenant.sales.index', 'label' => 'Vendas', 'icon' => 'sales', 'match' => 'tenant.sales.*'],
        ['route' => 'tenant.fixed-costs.index', 'label' => 'Custos fixos', 'icon' => 'fixed-costs', 'match' => 'tenant.fixed-costs.*'],
        ['route' => 'tenant.variable-costs.index', 'label' => 'Custos variáveis', 'icon' => 'variable-costs', 'match' => 'tenant.variable-costs.*'],
        ['route' => 'tenant.stock.index', 'label' => 'Estoque', 'icon' => 'stock', 'match' => 'tenant.stock.*'],
        ['route' => 'tenant.goals.edit', 'label' => 'Meta', 'icon' => 'goals', 'match' => 'tenant.goals.*'],
        ['route' => 'tenant.profile.edit', 'label' => 'Monte seu perfil', 'icon' => 'edit', 'match' => 'tenant.profile.*'],
    ];
    if ($tenant?->isPremium()) {
        $nav[] = ['route' => 'tenant.reports.monthly', 'label' => 'Relatório', 'icon' => 'reports', 'match' => 'tenant.reports.*'];
    }
@endphp
<body
    class="bg-paper font-sans text-ink min-h-screen"
    x-data="{
        sidebarOpen: false,
        aiOpen: false,
        aiQuestion: '',
        aiAnswer: '',
        aiLoading: false,
        cookieAccepted: localStorage.getItem('precifique_cookies') === '1',
        isDesktop: window.matchMedia('(min-width: 1024px)').matches,
        sendAi() {
            if (!this.aiQuestion?.trim() || this.aiLoading) return;
            this.aiLoading = true;
            fetch('{{ route('tenant.ai.chat') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ question: this.aiQuestion }),
            })
                .then(r => r.json())
                .then(d => { this.aiAnswer = d.answer || 'Sem resposta.'; })
                .catch(() => { window.toast?.error('Erro ao consultar a IA.'); })
                .finally(() => { this.aiLoading = false; });
        },
        initSidebar() {
            const saved = localStorage.getItem('precifique_sidebar');
            if (saved === '1') this.sidebarOpen = true;
            else if (saved === '0') this.sidebarOpen = false;
            else this.sidebarOpen = false;
            window.matchMedia('(min-width: 1024px)').addEventListener('change', (e) => {
                this.isDesktop = e.matches;
                if (!e.matches && this.sidebarOpen) document.body.style.overflow = 'hidden';
                else document.body.style.overflow = '';
            });
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('precifique_sidebar', this.sidebarOpen ? '1' : '0');
            if (!this.isDesktop) {
                document.body.style.overflow = this.sidebarOpen ? 'hidden' : '';
            }
        },
        closeSidebar() {
            this.sidebarOpen = false;
            localStorage.setItem('precifique_sidebar', '0');
            document.body.style.overflow = '';
        },
    }"
    x-init="initSidebar()"
    @keydown.escape.window="closeSidebar()"
>

    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeSidebar()"
        class="fixed inset-0 z-40 bg-ink/60 backdrop-blur-sm lg:bg-ink/20 lg:backdrop-blur-none cursor-pointer"
        aria-hidden="true"
    ></div>

    <aside
        x-ref="sidebar"
        @click.stop
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-[16.5rem] max-w-[85vw] ui-sidebar-premium text-white flex flex-col shadow-sidebar transition-transform duration-300 ease-out"
        :aria-hidden="!sidebarOpen"
    >
        <div class="px-4 py-4 border-b border-white/[0.06] flex items-center gap-2">
            <a
                href="{{ route('tenant.dashboard') }}"
                class="flex-1 min-w-0 flex items-center group"
                @click="closeSidebar()"
                aria-label="Precifique — ir ao dashboard"
            >
                <x-ui.logo
                    variant="sidebar"
                    class="w-full transition-opacity group-hover:opacity-90"
                />
            </a>
            <button
                type="button"
                @click="closeSidebar()"
                class="shrink-0 p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors -mr-1"
                aria-label="Fechar menu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
            @foreach($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="{{ request()->routeIs($item['match']) ? 'ui-sidebar-link-active' : 'ui-sidebar-link' }}"
               @click="closeSidebar()">
                <x-ui.nav-icon :name="$item['icon']" class="w-[1.125rem] h-[1.125rem] shrink-0 opacity-80" />
                {{ $item['label'] }}
            </a>
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
                    <a href="{{ route('tenant.billing.upgrade') }}" class="ui-badge-brand hover:bg-brand/20 transition-colors">Fazer upgrade</a>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 text-xs">
                <a href="{{ route('tenant.lgpd.portal') }}" class="ui-sidebar-link flex-1 justify-center py-2">Privacidade</a>
                <form method="POST" action="{{ route('tenant.logout') }}" class="flex-1">@csrf
                    <button type="submit" class="ui-sidebar-link w-full justify-center py-2 text-red-400/90 hover:text-red-300">Sair</button>
                </form>
            </div>
        </div>
    </aside>

    <div
        class="min-h-screen flex flex-col transition-[padding] duration-300 ease-out relative z-0"
        :class="sidebarOpen ? 'lg:pl-[16.5rem]' : ''"
    >
        <header class="sticky top-0 z-[60] ui-glass-header">
            <div class="flex items-center justify-between gap-4 px-4 md:px-8 h-14">
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        type="button"
                        x-ref="menuBtn"
                        @click.stop="toggleSidebar()"
                        class="relative p-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-colors"
                        :aria-expanded="sidebarOpen"
                        aria-label="Abrir menu"
                    >
                        <span class="sr-only" x-text="sidebarOpen ? 'Fechar menu' : 'Abrir menu'"></span>
                        <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                x-show="!sidebarOpen"
                                stroke-linecap="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                x-show="sidebarOpen"
                                x-cloak
                                stroke-linecap="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                    @hasSection('breadcrumb')
                    <div class="text-sm text-slate-500 truncate">@yield('breadcrumb')</div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($tenant?->isPremium())
                    <button @click="aiOpen=!aiOpen" class="ui-btn-outline px-3 py-2 text-xs hidden sm:inline-flex">Assistente</button>
                    @endif
                    @yield('header-actions')
                </div>
            </div>
        </header>

        @if(session('success'))
        <div class="px-4 md:px-8 pt-4 hidden" data-flash="success">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
        <div class="px-4 md:px-8 pt-4 hidden" data-flash="warning">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
        <div class="px-4 md:px-8 pt-4 hidden" data-flash="error">{{ session('error') }}</div>
        @endif

        <main class="flex-1 px-4 md:px-8 py-6 md:py-8 app-shell-bg">
            @isset($setupProgress)
            <x-ui.setup-progress :progress="$setupProgress" />
            @endisset
            @yield('content')
        </main>
    </div>

    @if($tenant?->isPremium())
    <div x-show="aiOpen" x-cloak x-transition class="fixed bottom-4 right-4 z-50 w-[calc(100%-2rem)] max-w-md">
        <div class="ui-card shadow-card-hover overflow-hidden">
            <div class="bg-ink text-white px-4 py-3 flex justify-between items-center">
                <div>
                    <p class="font-semibold text-sm">Assistente Precifique</p>
                    <p class="text-[11px] text-slate-400">Precificação e margens</p>
                </div>
                <button @click="aiOpen=false" class="text-slate-400 hover:text-white p-1" aria-label="Fechar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 min-h-[100px] max-h-52 overflow-y-auto text-sm text-slate-600 leading-relaxed" x-show="aiAnswer" x-text="aiAnswer"></div>
            <div x-show="aiLoading" x-cloak class="px-4 pb-2 text-xs text-slate-500 flex items-center gap-2">
                <span class="w-3 h-3 border-2 border-brand/30 border-t-brand rounded-full animate-spin"></span>
                Consultando especialista…
            </div>
            <div class="p-3 border-t border-slate-100 flex gap-2 bg-slate-50/50">
                <input x-model="aiQuestion" type="text" placeholder="Ex.: qual margem usar no meu nicho?" class="ui-input flex-1 py-2 text-sm" :disabled="aiLoading"
                    @keydown.enter="if(!aiLoading) sendAi()">
                <button class="ui-btn-primary px-4 py-2 text-sm" :disabled="aiLoading" @click="sendAi()">
                    <span x-show="!aiLoading">Enviar</span>
                    <span x-show="aiLoading" x-cloak>…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <div
        x-show="!cookieAccepted"
        x-cloak
        class="fixed bottom-0 inset-x-0 z-50 bg-ink text-white p-4 shadow-2xl transition-[left] duration-300"
        :class="sidebarOpen ? 'lg:left-[16.5rem]' : 'lg:left-0'"
    >
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm">Usamos cookies para melhorar sua experiência. <a href="{{ route('privacy') }}" class="text-brand underline">Política de Privacidade</a></p>
            <button @click="localStorage.setItem('precifique_cookies','1'); cookieAccepted=true" class="bg-brand hover:bg-brand-dark px-6 py-2 rounded-lg font-semibold text-ink shrink-0">Aceitar</button>
        </div>
    </div>

    @stack('scripts')
    <script>
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
