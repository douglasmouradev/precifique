<header id="landing-header" class="landing-nav">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-[4.25rem] flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center shrink-0 transition-opacity hover:opacity-90">
            <x-ui.logo variant="full" size="lg" dark />
        </a>
        <nav class="hidden md:flex gap-8" aria-label="{{ __('landing.nav_menu') }}">
            <a href="{{ route('home') }}#problema" class="landing-nav-link">{{ __('landing.nav_problem') }}</a>
            <a href="{{ route('home') }}#solucao" class="landing-nav-link">{{ __('landing.nav_solution') }}</a>
            <a href="{{ route('home') }}#planos" class="landing-nav-link">{{ __('landing.nav_plans') }}</a>
            <a href="{{ route('home') }}#faq" class="landing-nav-link">{{ __('landing.faq') }}</a>
        </nav>
        <div class="flex items-center gap-2 sm:gap-3 ml-auto">
            <x-ui.locale-switcher dark />
            <div class="hidden md:flex gap-3 items-center">
                <a href="{{ route('tenant.login') }}" class="landing-nav-link font-medium">{{ __('landing.login') }}</a>
                <a href="{{ route('tenant.register') }}" class="landing-btn-brand !py-2 !px-4 !text-sm">{{ __('landing.register') }}</a>
            </div>
            <button
                type="button"
                id="landing-mobile-menu-toggle"
                data-label-open="{{ __('landing.open_menu') }}"
                data-label-close="{{ __('landing.close_menu') }}"
                class="md:hidden relative z-[1] p-2.5 min-w-[2.75rem] min-h-[2.75rem] text-white hover:text-brand rounded-xl touch-manipulation cursor-pointer"
                aria-expanded="false"
                aria-controls="landing-mobile-menu"
                aria-label="{{ __('landing.open_menu') }}"
            >
                <svg data-menu-icon="open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg data-menu-icon="close" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div
        id="landing-mobile-menu"
        class="hidden md:hidden border-t border-white/10 bg-ink/95 backdrop-blur-2xl px-4 py-4 space-y-1"
        hidden
    >
        <a href="{{ route('home') }}#problema" class="block py-3 landing-nav-link">{{ __('landing.nav_problem') }}</a>
        <a href="{{ route('home') }}#solucao" class="block py-3 landing-nav-link">{{ __('landing.nav_solution') }}</a>
        <a href="{{ route('home') }}#planos" class="block py-3 landing-nav-link">{{ __('landing.nav_plans') }}</a>
        <a href="{{ route('home') }}#faq" class="block py-3 landing-nav-link">{{ __('landing.faq') }}</a>
        <div class="pt-4 border-t border-white/10 flex flex-col gap-2">
            <a href="{{ route('tenant.login') }}" class="text-center py-3 text-white border border-white/20 rounded-xl hover:border-brand transition-colors">{{ __('landing.login') }}</a>
            <a href="{{ route('tenant.register') }}" class="text-center py-3 landing-btn-brand">{{ __('landing.register') }}</a>
        </div>
    </div>
</header>
