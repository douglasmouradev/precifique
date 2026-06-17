@php
    $adminLinks = [
        ['route' => 'admin.dashboard', 'label' => __('admin.nav.dashboard'), 'match' => 'admin.dashboard'],
        ['route' => 'admin.tenants.index', 'label' => __('admin.nav.tenants'), 'match' => 'admin.tenants.*'],
        ['route' => 'admin.plans.index', 'label' => __('admin.nav.plans'), 'match' => 'admin.plans.*'],
        ['route' => 'admin.logs.index', 'label' => __('admin.nav.logs'), 'match' => 'admin.logs.*'],
        ['route' => 'admin.lgpd', 'label' => __('admin.nav.lgpd'), 'match' => 'admin.lgpd'],
        ['route' => 'admin.two-factor.show', 'label' => __('admin.nav.two_factor'), 'match' => 'admin.two-factor.*'],
    ];
@endphp

<nav id="admin-navigation" class="bg-white border-b border-slate-200/80 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- Logo + menu (desktop) --}}
            <div class="flex items-center min-w-0 flex-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center shrink-0 pr-5 mr-5 border-r border-slate-200 transition-opacity hover:opacity-90">
                    <x-ui.logo variant="full" size="sm" />
                </a>

                <div class="hidden md:flex items-center gap-0.5 shrink-0">
                    @foreach($adminLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="whitespace-nowrap px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs($link['match']) ? 'bg-brand/10 text-brand-dark' : 'text-slate-600 hover:text-ink hover:bg-slate-50' }}">
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex items-center gap-2 shrink-0">
                <x-ui.locale-switcher class="hidden sm:flex" />
                <a href="{{ route('home') }}" class="hidden sm:inline text-sm text-slate-500 hover:text-brand transition-colors whitespace-nowrap">{{ __('admin.nav.site') }}</a>

                <div class="hidden sm:block">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors max-w-[12rem]">
                                <span class="w-8 h-8 rounded-full bg-brand/15 text-brand-dark flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="truncate">{{ Auth::user()->name }}</span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Meu perfil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    Sair
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <button
                    type="button"
                    data-admin-nav-toggle
                    class="md:hidden p-2 rounded-lg hover:bg-slate-100 shrink-0 touch-manipulation"
                    aria-expanded="false"
                    aria-label="Menu"
                >
                    <svg data-admin-nav-icon-open class="h-6 w-6 text-slate-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg data-admin-nav-icon-close class="h-6 w-6 text-slate-600 hidden" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div data-admin-nav-panel class="hidden md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
        @foreach($adminLinks as $link)
        <a href="{{ route($link['route']) }}"
           class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs($link['match']) ? 'bg-brand/10 text-brand-dark' : 'text-slate-600' }}">
            {{ $link['label'] }}
        </a>
        @endforeach
        <div class="pt-3 mt-3 border-t border-slate-100 space-y-1">
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-slate-600">Meu perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 text-sm text-red-600">Sair</button>
            </form>
        </div>
    </div>
</nav>
