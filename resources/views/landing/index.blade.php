@extends('layouts.landing')

@section('title', 'Precifique — Precificação inteligente')

@section('content')
{{-- Intro + página — intro usa JS vanilla em landing.js --}}
<div id="landing-page">
    <x-landing.scroll-progress />
    <div
        id="landing-intro-overlay"
        data-intro-ready="{{ __('landing.intro_ready') }}"
        data-intro-preparing="{{ __('landing.intro_preparing') }}"
        class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-ink text-white px-6"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('landing.intro_loading') }}"
        aria-hidden="false"
    >
        <button
            type="button"
            id="landing-intro-skip"
            style="position:fixed;top:1rem;right:1rem;z-index:110"
            class="text-sm font-semibold text-white bg-black/50 hover:bg-black/70 border border-white/25 px-4 py-2 rounded-lg backdrop-blur-sm shadow-lg"
        >{{ __('landing.intro_skip') }}</button>
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(ellipse_at_center,#00C896_0%,transparent_65%)]"></div>

        <div class="relative z-10 flex flex-col items-center w-full max-w-2xl">
            <x-ui.logo variant="full" size="xl" dark class="mb-12" />

            <p class="text-brand text-xs font-semibold tracking-[0.25em] uppercase mb-6">{{ __('landing.intro_loading') }}</p>

            <p
                id="landing-intro-phrase"
                class="font-display text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center leading-snug min-h-[4.5rem] sm:min-h-[5.5rem]"
            >
                {{ __('landing.intro_question') }}
            </p>

            <div class="w-full mt-10">
                <div class="flex justify-between text-xs text-gray-500 mb-2 tabular-nums">
                    <span id="landing-intro-status">{{ __('landing.intro_preparing') }}</span>
                    <span><span id="landing-intro-pct">0</span>%</span>
                </div>
                <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                    <div
                        id="landing-intro-bar"
                        class="h-full bg-brand rounded-full transition-[width] duration-150 ease-out shadow-[0_0_12px_rgba(0,200,150,0.6)]"
                        style="width: 0%"
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
    id="landing-header"
    class="fixed top-0 inset-x-0 z-[110] bg-ink/80 backdrop-blur-xl border-b border-white/10 transition-shadow duration-300"
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
        <div class="flex items-center gap-2 sm:gap-3 ml-auto">
            <x-ui.locale-switcher dark />
            <div class="hidden md:flex gap-3 items-center">
                <a href="{{ route('tenant.login') }}" class="text-sm text-white hover:text-brand">{{ __('landing.login') }}</a>
                <a href="{{ route('tenant.register') }}" class="bg-brand text-ink px-4 py-2 rounded-lg text-sm font-semibold hover:bg-brand-dark">{{ __('landing.register') }}</a>
            </div>
            <button
            type="button"
            id="landing-mobile-menu-toggle"
            data-label-open="{{ __('landing.open_menu') }}"
            data-label-close="{{ __('landing.close_menu') }}"
            class="md:hidden p-2.5 min-w-[2.75rem] min-h-[2.75rem] text-white hover:text-brand rounded-lg touch-manipulation"
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
        class="hidden md:hidden border-t border-white/10 bg-ink/95 backdrop-blur-xl px-4 py-4 space-y-3"
        hidden
    >
        <a href="#problema" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.nav_problem') }}</a>
        <a href="#solucao" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.nav_solution') }}</a>
        <a href="#planos" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.nav_plans') }}</a>
        <a href="#faq" class="block py-2 text-gray-300 hover:text-brand">{{ __('landing.faq') }}</a>
        <div class="py-2 flex justify-center">
            <x-ui.locale-switcher dark />
        </div>
        <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
            <a href="{{ route('tenant.login') }}" class="text-center py-2.5 text-white border border-white/20 rounded-lg">{{ __('landing.login') }}</a>
            <a href="{{ route('tenant.register') }}" class="text-center py-2.5 bg-brand text-ink rounded-lg font-semibold">{{ __('landing.register') }}</a>
        </div>
    </div>
</header>

<main class="pt-[4.25rem] landing-3d-root" data-landing-3d>
    {{-- Hero --}}
    <section data-scroll-3d-hero class="bg-ink text-white py-24 md:py-32 relative overflow-hidden" style="background-color:#0D0D0D">
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
            <x-landing.reveal :delay="120" class="card-3d bg-ink rounded-2xl p-6 shadow-2xl text-white" id="landing-pricing-demo" data-cost="25.5">
                <p class="text-xs text-gray-400 mb-3">{{ __('landing.demo_hint') }}</p>
                <div class="flex gap-2 mb-4">
                    @foreach([30, 50, 70, 100] as $margin)
                    <button type="button" data-demo-margin="{{ $margin }}"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors bg-white/10 text-gray-300 hover:bg-white/20"
                    >{{ $margin }}%</button>
                    @endforeach
                </div>
                <div class="bg-brand/20 rounded-xl p-4 mb-4">
                    <p class="text-sm text-gray-400">{{ __('landing.demo_final_price') }}</p>
                    <p class="font-display text-4xl font-bold text-brand" data-demo-price></p>
                </div>
                <div class="space-y-2 text-sm text-gray-300">
                    <div class="flex justify-between"><span>{{ __('landing.demo_production_cost') }}</span><span data-demo-cost></span></div>
                    <div class="flex justify-between text-brand font-semibold"><span>{{ __('landing.demo_profit') }} (<span data-demo-margin-label>50</span>%)</span><span data-demo-profit></span></div>
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
                        {{ $plan->slug === 'premium' ? __('landing.plans_subscribe_premium') : __('landing.plans_subscribe_basic') }}
                    </a>
                </x-landing.reveal>
                @empty
                <p class="col-span-2 text-center text-gray-400">{{ __('landing.plans_configuring') }}</p>
                @endforelse
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Comparativo de planos --}}
    <x-landing.scroll-3d-section intensity="subtle" class="py-16 bg-paper border-y border-slate-100">
        <div class="max-w-4xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-center mb-10 text-ink">{{ __('landing.compare_title') }}</h2>
            </x-landing.reveal>
            <x-landing.reveal :delay="80" class="overflow-x-auto">
            <table class="w-full text-sm bg-white rounded-2xl shadow-card overflow-hidden border border-slate-200/70">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">{{ __('landing.compare_feature') }}</th>
                        <th class="px-5 py-4 font-semibold">Basic</th>
                        <th class="px-5 py-4 font-semibold text-brand-dark">Premium</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach(__('landing.compare_rows') as $row)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-ink">{{ $row['label'] }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $row['basic'] }}</td>
                        <td class="px-5 py-3.5 font-semibold text-brand-dark">{{ $row['premium'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </x-landing.reveal>
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
            <x-landing.reveal :delay="120" class="mt-12 text-center">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">{{ __('landing.logos_title') }}</p>
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach(__('landing.logos') as $logo)
                    <span class="px-4 py-2 rounded-full bg-slate-100 text-slate-600 text-sm font-medium border border-slate-200/80">{{ $logo }}</span>
                    @endforeach
                </div>
            </x-landing.reveal>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Depoimentos --}}
    <x-landing.scroll-3d-section intensity="subtle" class="py-20 bg-ink text-white">
        <div class="max-w-6xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-center mb-12">{{ __('landing.testimonials_title') }}</h2>
            </x-landing.reveal>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach(__('landing.testimonials') as $i => $t)
                <x-landing.reveal :delay="$i * 100" class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-brand/20 text-brand font-bold flex items-center justify-center text-sm">{{ $t['initials'] }}</div>
                        <div>
                            <p class="font-semibold">{{ $t['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ $t['business'] }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-300 leading-relaxed">"{{ $t['text'] }}"</p>
                </x-landing.reveal>
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
        <div class="max-w-3xl mx-auto px-4" id="landing-faq">
            <x-landing.reveal>
            <h2 class="font-display text-3xl font-bold text-center mb-12">{{ __('landing.faq') }}</h2>
            </x-landing.reveal>
            @foreach(__('landing.faq_items') as $i => $faq)
            <x-landing.reveal :delay="$i * 60" class="border-b border-gray-200">
                <button
                    type="button"
                    id="faq-btn-{{ $i }}"
                    data-faq-toggle="{{ $i }}"
                    class="w-full py-4 text-left font-semibold flex justify-between touch-manipulation"
                    aria-expanded="false"
                    aria-controls="faq-panel-{{ $i }}"
                >
                    {{ $faq['q'] }}
                    <span data-faq-icon aria-hidden="true">+</span>
                </button>
                <div id="faq-panel-{{ $i }}" data-faq-panel="{{ $i }}" class="hidden pb-4 text-gray-600" role="region" aria-labelledby="faq-btn-{{ $i }}">{{ $faq['a'] }}</div>
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

<div
    id="landing-mobile-cta"
    class="md:hidden fixed bottom-0 inset-x-0 z-[105] px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] bg-ink/95 backdrop-blur-xl border-t border-white/10 shadow-[0_-8px_32px_rgba(0,0,0,0.35)] opacity-0 translate-y-full pointer-events-none transition-all duration-300"
>
    <a
        href="{{ route('tenant.register') }}"
        class="flex items-center justify-center w-full min-h-[3rem] rounded-xl bg-brand text-ink font-bold text-sm shadow-premium-glow hover:bg-brand-dark transition-colors"
    >{{ __('landing.mobile_cta') }}</a>
</div>
</div>{{-- fim #landing-page --}}
<style>.clip-hex{clip-path:polygon(25% 0%,75% 0%,100% 50%,75% 100%,25% 100%,0% 50%)}</style>
@endsection
