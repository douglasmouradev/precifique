@extends('layouts.landing')

@section('title', 'Precifique — Precificação inteligente')

@section('content')
{{-- Abertura com carregamento --}}
<div
    x-data="{
        showIntro: !sessionStorage.getItem('precifique_intro_seen'),
        progress: 0,
        phraseVisible: false,
        loadingDone: false,
        scrollProgress: 0,
        focusTrapHandler: null,
        init() {
            this.initScrollProgress();
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.showIntro = false;
                sessionStorage.setItem('precifique_intro_seen', '1');
                return;
            }
            if (!this.showIntro) return;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                this.$refs.introDialog?.focus();
                this.focusTrapHandler = (e) => this.handleFocusTrap(e);
                document.addEventListener('keydown', this.focusTrapHandler);
            });
            const duration = 1600;
            const start = performance.now();
            const easeOut = (t) => 1 - Math.pow(1 - t, 3);
            const step = (now) => {
                const t = Math.min(1, (now - start) / duration);
                this.progress = Math.round(easeOut(t) * 100);
                if (this.progress >= 20) {
                    this.phraseVisible = true;
                }
                if (t < 1) {
                    requestAnimationFrame(step);
                } else {
                    this.loadingDone = true;
                    setTimeout(() => this.closeIntro(), 700);
                }
            };
            requestAnimationFrame(step);
        },
        initScrollProgress() {
            const update = () => {
                const el = document.documentElement;
                const max = el.scrollHeight - el.clientHeight;
                this.scrollProgress = max > 0 ? Math.min(100, (el.scrollTop / max) * 100) : 0;
            };
            window.addEventListener('scroll', update, { passive: true });
            update();
        },
        closeIntro() {
            sessionStorage.setItem('precifique_intro_seen', '1');
            this.showIntro = false;
            document.body.style.overflow = '';
            if (this.focusTrapHandler) {
                document.removeEventListener('keydown', this.focusTrapHandler);
                this.focusTrapHandler = null;
            }
        },
        handleFocusTrap(e) {
            if (!this.showIntro) return;
            if (e.key === 'Escape') { this.closeIntro(); return; }
            if (e.key !== 'Tab') return;
            const container = this.$refs.introDialog;
            if (!container) return;
            const focusable = container.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])');
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
    }"
>
    <x-landing.scroll-progress />
    <div
        x-show="showIntro"
        x-ref="introDialog"
        tabindex="-1"
        x-transition:leave="transition ease-in duration-700"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-ink text-white px-6 outline-none"
        role="dialog"
        aria-modal="true"
        :aria-label="@js(__('landing.intro_loading'))"
        aria-busy="true"
    >
        <button type="button" @click="closeIntro()" class="absolute top-6 right-6 z-20 text-sm text-gray-400 hover:text-white underline focus:outline-none focus:ring-2 focus:ring-brand rounded px-2 py-1">{{ __('landing.intro_skip') }}</button>
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(ellipse_at_center,#00C896_0%,transparent_65%)]"></div>

        <div class="relative z-10 flex flex-col items-center w-full max-w-2xl">
            <x-ui.logo variant="full" size="xl" dark class="mb-12" />

            <p class="text-brand text-xs font-semibold tracking-[0.25em] uppercase mb-6">{{ __('landing.intro_loading') }}</p>

            <p
                class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center leading-snug min-h-[4.5rem] sm:min-h-[5.5rem] transition-all duration-700 ease-out"
                :class="phraseVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                {{ __('landing.intro_question') }}
            </p>

            <div class="w-full mt-10">
                <div class="flex justify-between text-xs text-gray-500 mb-2 tabular-nums">
                    <span x-text="loadingDone ? @js(__('landing.intro_ready')) : @js(__('landing.intro_preparing'))"></span>
                    <span x-text="progress + '%'"></span>
                </div>
                <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-brand rounded-full transition-[width] duration-150 ease-out shadow-[0_0_12px_rgba(0,200,150,0.6)]"
                        :style="'width:' + progress + '%'"
                    ></div>
                </div>
            </div>

            <div class="mt-8 flex gap-1.5" aria-hidden="true">
                <span class="w-2 h-2 rounded-full bg-brand animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-2 h-2 rounded-full bg-brand animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-2 h-2 rounded-full bg-brand animate-bounce" style="animation-delay: 300ms"></span>
            </div>
        </div>
    </div>

<header
    class="fixed top-0 inset-x-0 z-40 bg-ink/80 backdrop-blur-xl border-b border-white/10 transition-shadow duration-300"
    x-data="{ scrolled: false, menuOpen: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 16 }, { passive: true })"
    :class="scrolled && 'shadow-[0_8px_30px_-12px_rgba(0,0,0,0.45)]'"
>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-[4.25rem] flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center shrink-0 transition-opacity hover:opacity-90">
            <x-ui.logo variant="full" size="lg" dark />
        </a>
        <nav class="hidden md:flex gap-6 text-sm text-gray-300">
            <a href="#problema" class="hover:text-brand">{{ __('landing.nav_problem') }}</a>
            <a href="#solucao" class="hover:text-brand">{{ __('landing.nav_solution') }}</a>
            <a href="#planos" class="hover:text-brand">{{ __('landing.nav_plans') }}</a>
            <a href="#faq" class="hover:text-brand">{{ __('landing.faq') }}</a>
        </nav>
        <div class="hidden md:flex gap-3 items-center">
            <x-ui.locale-switcher />
            <a href="{{ route('tenant.login') }}" class="text-sm text-white hover:text-brand">{{ __('landing.login') }}</a>
            <a href="{{ route('tenant.register') }}" class="bg-brand text-ink px-4 py-2 rounded-lg text-sm font-semibold hover:bg-brand-dark">{{ __('landing.register') }}</a>
        </div>
        <button
            type="button"
            class="md:hidden p-2 text-white hover:text-brand rounded-lg"
            @click="menuOpen = !menuOpen"
            :aria-expanded="menuOpen"
            aria-label="{{ __('landing.open_menu') }}"
        >
            <svg x-show="!menuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="menuOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div
        x-show="menuOpen"
        x-cloak
        x-transition
        @click.outside="menuOpen = false"
        class="md:hidden border-t border-white/10 bg-ink/95 backdrop-blur-xl px-4 py-4 space-y-3"
    >
        <a href="#problema" @click="menuOpen = false" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.nav_problem') }}</a>
        <a href="#solucao" @click="menuOpen = false" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.nav_solution') }}</a>
        <a href="#planos" @click="menuOpen = false" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.nav_plans') }}</a>
        <a href="#faq" @click="menuOpen = false" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.faq') }}</a>
        <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
            <a href="{{ route('tenant.login') }}" class="text-center py-2.5 text-white border border-white/20 rounded-lg">{{ __('landing.login') }}</a>
            <a href="{{ route('tenant.register') }}" class="text-center py-2.5 bg-brand text-ink rounded-lg font-semibold">{{ __('landing.register') }}</a>
        </div>
    </div>
</header>

<main class="pt-[4.25rem] landing-3d-root" data-landing-3d>
    {{-- Hero --}}
    <section data-scroll-3d-hero class="bg-ink text-white py-24 md:py-32 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_30%_50%,#00C896,transparent_50%)]"></div>
        <div class="hero-orb absolute top-[18%] right-[12%] w-64 h-64 md:w-80 md:h-80 rounded-full bg-brand/25 blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="scroll-3d-section__inner max-w-6xl mx-auto px-4 relative">
            <div>
                <p class="text-brand font-semibold mb-4 tracking-wide uppercase text-sm">{{ __('landing.hero_eyebrow') }}</p>
                <h1 class="font-display text-4xl md:text-5xl font-bold leading-tight max-w-3xl">
                    {{ __('landing.hero_headline') }}
                </h1>
                <p class="mt-6 text-gray-300 text-lg max-w-xl">{{ __('landing.hero_desc') }}</p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('tenant.register') }}" class="bg-brand text-ink px-8 py-4 rounded-xl font-bold text-lg hover:bg-brand-dark transition">{{ __('landing.register') }}</a>
                    <a href="#solucao" class="border border-white/30 px-8 py-4 rounded-xl font-semibold hover:border-brand transition-colors">{{ __('landing.see_how') }}</a>
                </div>
                <p class="mt-8 text-gray-400 text-sm">
                    <span class="text-brand font-semibold">{{ __('landing.trial_badge', ['days' => config('tenancy.trial_days', 14)]) }}</span>
                    · {{ __('landing.trial_footer') }}
                </p>
            </div>
        </div>
        <div class="absolute bottom-8 inset-x-0 flex flex-col items-center gap-2 text-gray-500 pointer-events-none animate-bounce" aria-hidden="true">
            <span class="text-[10px] uppercase tracking-[0.2em]">{{ __('landing.scroll_hint') }}</span>
            <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </div>
    </section>

    {{-- Problema --}}
    <x-landing.scroll-3d-section id="problema" intensity="medium" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-center mb-12">{{ __('landing.problem_title') }}</h2>
            </x-landing.reveal>
            <div class="grid md:grid-cols-3 gap-8">
                @php $problemIcons = ['trend-down', 'money', 'alert']; @endphp
                @foreach(__('landing.problem_cards') as $i => $card)
                <x-landing.reveal :delay="$i * 100" class="card-3d p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-4">
                        <x-ui.nav-icon :name="$problemIcons[$i]" class="w-6 h-6" />
                    </div>
                    <h3 class="font-display font-bold text-xl mb-2">{{ $card['title'] }}</h3>
                    <p class="text-gray-600">{{ $card['text'] }}</p>
                </x-landing.reveal>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Solução --}}
    <x-landing.scroll-3d-section id="solucao" intensity="medium" class="py-20 bg-paper">
        <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
            <x-landing.reveal :delay="0">
                <h2 class="font-display text-3xl font-bold mb-6">{{ __('landing.solution_title') }}</h2>
                <ul class="space-y-4">
                    @foreach(__('landing.solution_features') as $f)
                    <li class="flex items-center gap-3"><span class="w-6 h-6 bg-brand rounded-full flex items-center justify-center text-ink text-xs">✓</span>{{ $f }}</li>
                    @endforeach
                </ul>
            </x-landing.reveal>
            <x-landing.reveal :delay="120" class="card-3d bg-ink rounded-2xl p-6 shadow-2xl text-white" x-data="{
                cost: 25.50,
                margin: 50,
                get profit() { return this.cost * (this.margin / 100); },
                get price() { return this.cost + this.profit; },
                fmt(n) { return n.toLocaleString(document.documentElement.lang || 'pt-BR', { style: 'currency', currency: 'BRL' }); }
            }">
                <p class="text-xs text-gray-400 mb-3">{{ __('landing.demo_hint') }}</p>
                <div class="flex gap-2 mb-4">
                    <template x-for="m in [30, 50, 70, 100]" :key="m">
                        <button type="button" @click="margin = m"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors"
                            :class="margin === m ? 'bg-brand text-ink' : 'bg-white/10 text-gray-300 hover:bg-white/20'"
                            x-text="m + '%'"></button>
                    </template>
                </div>
                <div class="bg-brand/20 rounded-xl p-4 mb-4">
                    <p class="text-sm text-gray-400">{{ __('landing.demo_final_price') }}</p>
                    <p class="font-display text-4xl font-bold text-brand" x-text="fmt(price)"></p>
                </div>
                <div class="space-y-2 text-sm text-gray-300">
                    <div class="flex justify-between"><span>{{ __('landing.demo_production_cost') }}</span><span x-text="fmt(cost)"></span></div>
                    <div class="flex justify-between text-brand font-semibold"><span x-text="'{{ __('landing.demo_profit') }} (' + margin + '%)'"></span><span x-text="'+ ' + fmt(profit)"></span></div>
                </div>
            </x-landing.reveal>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Nichos --}}
    <x-landing.scroll-3d-section intensity="medium" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold mb-12">{{ __('landing.niches_title') }}</h2>
            </x-landing.reveal>
            <div class="grid md:grid-cols-3 gap-8">
                @php $nicheIcons = ['food', 'service', 'craft']; @endphp
                @foreach(__('landing.niches') as $i => $n)
                <x-landing.reveal :delay="$i * 100" class="card-3d p-8 rounded-2xl bg-paper border-2 border-transparent hover:border-brand transition-colors">
                    <div class="w-14 h-14 rounded-2xl bg-brand/10 text-brand flex items-center justify-center mb-4 mx-auto">
                        <x-ui.nav-icon :name="$nicheIcons[$i]" class="w-7 h-7" />
                    </div>
                    <h3 class="font-display font-bold text-xl">{{ $n['title'] }}</h3>
                    <p class="text-gray-600 mt-2">{{ $n['text'] }}</p>
                </x-landing.reveal>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Planos --}}
    <x-landing.scroll-3d-section id="planos" intensity="medium" class="py-20 bg-ink text-white">
        <div class="max-w-6xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-center mb-12">{{ __('landing.plans_title') }}</h2>
            </x-landing.reveal>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                @forelse($plans as $i => $plan)
                <x-landing.reveal
                    :delay="$i * 120"
                    class="card-3d rounded-2xl p-8 {{ $plan->slug === 'premium' ? 'bg-brand text-ink ring-4 ring-brand' : 'bg-white/5 border border-white/10' }}"
                    data-scroll-3d-pricing
                    data-direction="{{ $i % 2 === 0 ? 'left' : 'right' }}"
                >
                    @if($plan->slug === 'premium')
                    <span class="text-xs font-bold uppercase bg-ink text-brand px-2 py-1 rounded">{{ __('landing.plans_ai') }}</span>
                    @endif
                    <h3 class="font-display text-2xl font-bold mt-4">{{ $plan->name }}</h3>
                    <p class="text-3xl font-bold mt-2">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}<span class="text-base font-normal">/mês</span></p>
                    <ul class="mt-6 space-y-2 text-sm">
                        @foreach($plan->features ?? [] as $feature)
                        <li>✓ {{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('tenant.register') }}" class="mt-8 block text-center py-3 rounded-xl font-bold {{ $plan->slug === 'premium' ? 'bg-ink text-white' : 'bg-brand text-ink' }}">
                        {{ $plan->price_monthly > 0 ? __('landing.plans_subscribe') : __('landing.register') }}
                    </a>
                </x-landing.reveal>
                @empty
                <p class="col-span-2 text-center text-gray-400">{{ __('landing.plans_configuring') }}</p>
                @endforelse
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Prova social --}}
    <x-landing.scroll-3d-section intensity="subtle" class="py-16 bg-white border-y border-slate-100">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                @foreach(__('landing.stats') as $stat)
                <div class="p-4">
                    <p class="font-display text-2xl font-bold text-brand-dark">{{ $stat['value'] }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Casos de uso --}}
    <x-landing.scroll-3d-section intensity="subtle" class="py-20 bg-paper">
        <div class="max-w-6xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-center mb-3">{{ __('landing.use_cases_title') }}</h2>
            <p class="text-center text-slate-500 mb-12 max-w-xl mx-auto">{{ __('landing.use_cases_subtitle') }}</p>
            </x-landing.reveal>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach(__('landing.use_cases') as $i => $t)
                <x-landing.reveal :delay="$i * 100" class="card-3d bg-white p-6 rounded-2xl shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-4">
                        <x-ui.nav-icon name="spark" class="w-5 h-5" />
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-800 mb-2">{{ $t['title'] }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $t['text'] }}</p>
                </x-landing.reveal>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- FAQ --}}
    <x-landing.scroll-3d-section id="faq" intensity="subtle" class="py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4" x-data="{ open: null }">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-center mb-12">{{ __('landing.faq') }}</h2>
            </x-landing.reveal>
            @foreach(__('landing.faq_items') as $i => $faq)
            <x-landing.reveal :delay="$i * 60" class="border-b border-gray-200">
                <button type="button" id="faq-btn-{{ $i }}" @click="open = open === {{ $i }} ? null : {{ $i }}"
                    class="w-full py-4 text-left font-semibold flex justify-between"
                    :aria-expanded="open === {{ $i }}"
                    aria-controls="faq-panel-{{ $i }}">
                    {{ $faq['q'] }}
                    <span aria-hidden="true" x-text="open === {{ $i }} ? '−' : '+'"></span>
                </button>
                <div id="faq-panel-{{ $i }}" x-show="open === {{ $i }}" x-collapse class="pb-4 text-gray-600" role="region" aria-labelledby="faq-btn-{{ $i }}">{{ $faq['a'] }}</div>
            </x-landing.reveal>
            @endforeach
        </div>
    </x-landing.scroll-3d-section>

    {{-- CTA Final --}}
    <x-landing.scroll-3d-section intensity="subtle" class="py-20 bg-brand">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-ink">{{ __('landing.cta_final_title') }}</h2>
            <a href="{{ route('tenant.register') }}" class="mt-8 inline-block bg-ink text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-slate-800 transition-colors">{{ __('landing.cta_final_button') }}</a>
            </x-landing.reveal>
        </div>
    </x-landing.scroll-3d-section>
</main>

<footer class="bg-ink text-gray-400 py-12">
    <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between gap-6">
        <x-ui.logo variant="full" size="md" dark />
        <nav class="flex gap-6 text-sm">
            <a href="{{ route('privacy') }}" class="hover:text-brand">{{ __('landing.footer_privacy') }}</a>
            <a href="{{ route('terms') }}" class="hover:text-brand">{{ __('landing.footer_terms') }}</a>
            <a href="{{ route('tenant.login') }}" class="hover:text-brand">{{ __('landing.login') }}</a>
        </nav>
        <p class="text-sm">© {{ date('Y') }} Precifique. {{ __('landing.footer_rights') }}</p>
    </div>
</footer>
</div>{{-- fim abertura --}}
<style>.clip-hex{clip-path:polygon(25% 0%,75% 0%,100% 50%,75% 100%,25% 100%,0% 50%)}</style>
@endsection
