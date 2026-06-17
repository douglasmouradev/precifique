@extends('layouts.landing')

@section('title', __('landing.og_title'))

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect(__('landing.faq_items'))->map(fn ($faq) => [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $faq['a'],
        ],
    ])->values()->all(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

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
                <div class="flex justify-between text-xs text-slate-500 mb-2 tabular-nums">
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

        </div>
    </div>

<x-landing.header />

<main class="pt-[4.25rem] landing-3d-root" data-landing-3d>
    {{-- Hero --}}
    <section data-scroll-3d-hero class="bg-ink text-white landing-section md:py-36 relative overflow-hidden">
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(ellipse_80%_60%_at_30%_40%,rgba(0,200,150,0.35),transparent_55%)]"></div>
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(0,0,0,0)_0%,rgba(10,10,10,0.4)_100%)]"></div>
        <div class="hero-orb absolute top-[15%] right-[10%] w-72 h-72 md:w-96 md:h-96 rounded-full bg-brand/20 blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="scroll-3d-section__inner max-w-6xl mx-auto px-4 relative">
            <div class="max-w-3xl">
                <p class="landing-eyebrow mb-5">{{ __('landing.hero_eyebrow') }}</p>
                <h1 class="landing-section-title text-white max-w-3xl">
                    {{ __('landing.hero_headline') }}
                </h1>
                <p class="mt-6 text-slate-300 text-lg md:text-xl max-w-xl leading-relaxed">{{ __('landing.hero_desc') }}</p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('tenant.register') }}" class="landing-btn-brand-lg">{{ __('landing.register') }}</a>
                    <a href="#solucao" class="landing-btn-ghost">{{ __('landing.see_how') }}</a>
                </div>
                <p class="mt-8 text-slate-400 text-sm">
                    <span class="text-brand font-semibold">{{ __('landing.trial_badge', ['days' => config('tenancy.trial_days', 14)]) }}</span>
                    · {{ __('landing.trial_footer') }}
                </p>
            </div>
        </div>
        <div class="absolute bottom-8 inset-x-0 flex flex-col items-center gap-2 text-slate-500 pointer-events-none" aria-hidden="true">
            <span class="text-[10px] uppercase tracking-[0.2em]">{{ __('landing.scroll_hint') }}</span>
            <svg class="w-5 h-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </div>
    </section>

    {{-- Problema --}}
    <x-landing.scroll-3d-section id="problema" intensity="medium" class="landing-section bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="landing-section-title text-center mb-14">{{ __('landing.problem_title') }}</h2>
            </x-landing.reveal>
            <div class="grid md:grid-cols-3 gap-6 md:gap-8">
                @php $problemIcons = ['trend-down', 'money', 'alert']; @endphp
                @foreach(__('landing.problem_cards') as $i => $card)
                <x-landing.reveal :delay="$i * 100" class="card-3d landing-card !p-6">
                    <div class="w-12 h-12 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-4">
                        <x-ui.nav-icon :name="$problemIcons[$i]" class="w-6 h-6" />
                    </div>
                    <h3 class="font-display font-bold text-xl mb-2">{{ $card['title'] }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $card['text'] }}</p>
                </x-landing.reveal>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Solução --}}
    <x-landing.scroll-3d-section id="solucao" intensity="medium" class="landing-section bg-paper">
        <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12 lg:gap-16 items-center">
            <x-landing.reveal :delay="0">
                <h2 class="landing-section-title mb-6">{{ __('landing.solution_title') }}</h2>
                <ul class="space-y-4">
                    @foreach(__('landing.solution_features') as $f)
                    <li class="flex items-center gap-3"><span class="w-6 h-6 bg-brand rounded-full flex items-center justify-center text-ink text-xs">✓</span>{{ $f }}</li>
                    @endforeach
                </ul>
            </x-landing.reveal>
            <x-landing.reveal :delay="120" class="card-3d landing-card-dark bg-ink text-white shadow-elevated" id="landing-pricing-demo" data-cost="25.5">
                <p class="text-xs text-slate-400 mb-3">{{ __('landing.demo_hint') }}</p>
                <div class="flex gap-2 mb-4">
                    @foreach([30, 50, 70, 100] as $margin)
                    <button type="button" data-demo-margin="{{ $margin }}"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors bg-white/10 text-slate-300 hover:bg-white/20"
                    >{{ $margin }}%</button>
                    @endforeach
                </div>
                <div class="bg-brand/20 rounded-xl p-4 mb-4">
                    <p class="text-sm text-slate-400">{{ __('landing.demo_final_price') }}</p>
                    <p class="font-display text-4xl font-bold text-brand" data-demo-price></p>
                </div>
                <div class="space-y-2 text-sm text-slate-300">
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
                    <p class="text-slate-600 mt-2">{{ $n['text'] }}</p>
                </x-landing.reveal>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Planos --}}
    <x-landing.scroll-3d-section id="planos" intensity="medium" class="landing-section bg-ink text-white">
        <div class="max-w-6xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="landing-section-title text-center mb-14 text-white">{{ __('landing.plans_title') }}</h2>
            </x-landing.reveal>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                @forelse($plans as $i => $plan)
                <x-landing.reveal
                    :delay="$i * 120"
                    class="card-3d rounded-2xl p-8 {{ $plan->slug === 'premium' ? 'bg-brand text-ink shadow-brand-glow ring-2 ring-brand/30' : 'landing-card-dark' }}"
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
                    <a href="{{ route('tenant.register') }}" class="mt-8 block text-center py-3.5 rounded-xl font-bold transition-all {{ $plan->slug === 'premium' ? 'bg-ink text-white hover:bg-slate-800' : 'landing-btn-brand !w-full' }}">
                        {{ $plan->slug === 'premium' ? __('landing.plans_subscribe_premium') : __('landing.plans_subscribe_basic') }}
                    </a>
                </x-landing.reveal>
                @empty
                <p class="col-span-2 text-center text-slate-400">{{ __('landing.plans_configuring') }}</p>
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
                    <p class="landing-stat-value">{{ $stat['value'] }}</p>
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
            <h2 class="landing-section-title text-center mb-14 text-white">{{ __('landing.testimonials_title') }}</h2>
            </x-landing.reveal>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach(__('landing.testimonials') as $i => $t)
                <x-landing.reveal :delay="$i * 100" class="landing-card-dark">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-full bg-brand/20 text-brand font-bold flex items-center justify-center text-sm">{{ $t['initials'] }}</div>
                        <div>
                            <p class="font-semibold">{{ $t['name'] }}</p>
                            <p class="text-xs text-slate-400">{{ $t['business'] }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-300 leading-relaxed">"{{ $t['text'] }}"</p>
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
                    <p class="text-slate-600 text-sm leading-relaxed">{{ $t['text'] }}</p>
                </x-landing.reveal>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- Prévia do produto --}}
    <x-landing.scroll-3d-section id="preview" intensity="subtle" class="py-20 bg-white border-y border-slate-100">
        <div class="max-w-6xl mx-auto px-4">
            <x-landing.reveal>
            <h2 class="landing-section-title text-center mb-3">{{ __('landing.preview_title') }}</h2>
            <p class="text-center text-slate-500 mb-12 max-w-xl mx-auto">{{ __('landing.preview_subtitle') }}</p>
            </x-landing.reveal>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach(__('landing.preview_cards') as $i => $card)
                <x-landing.reveal :delay="$i * 80" class="card-3d landing-card overflow-hidden !p-0">
                    <div class="relative aspect-[16/10] bg-slate-100 border-b border-slate-200/80 overflow-hidden">
                        <x-landing.preview-art :name="$card['preview']" :label="$card['title'].' — Precifique'" />
                    </div>
                    <div class="p-5">
                        <h3 class="font-display font-bold text-lg text-ink mb-1">{{ $card['title'] }}</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $card['text'] }}</p>
                    </div>
                </x-landing.reveal>
                @endforeach
            </div>
        </div>
    </x-landing.scroll-3d-section>

    {{-- FAQ --}}
    <x-landing.scroll-3d-section id="faq" intensity="subtle" class="landing-section bg-white">
        <div class="max-w-3xl mx-auto px-4" id="landing-faq">
            <x-landing.reveal>
            <h2 class="landing-section-title text-center mb-12">{{ __('landing.faq') }}</h2>
            </x-landing.reveal>
            @foreach(__('landing.faq_items') as $i => $faq)
            <x-landing.reveal :delay="$i * 60" class="landing-faq-item">
                <button
                    type="button"
                    id="faq-btn-{{ $i }}"
                    data-faq-toggle="{{ $i }}"
                    class="landing-faq-trigger"
                    aria-expanded="false"
                    aria-controls="faq-panel-{{ $i }}"
                >
                    {{ $faq['q'] }}
                    <span data-faq-icon aria-hidden="true" class="text-brand text-xl leading-none">+</span>
                </button>
                <div id="faq-panel-{{ $i }}" data-faq-panel="{{ $i }}" class="hidden pb-5 text-slate-600 leading-relaxed" role="region" aria-labelledby="faq-btn-{{ $i }}">{{ $faq['a'] }}</div>
            </x-landing.reveal>
            @endforeach
        </div>
    </x-landing.scroll-3d-section>

    {{-- CTA Final --}}
    <x-landing.scroll-3d-section intensity="subtle" class="landing-section bg-brand">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <x-landing.reveal>
            <h2 class="landing-section-title text-ink">{{ __('landing.cta_final_title') }}</h2>
            <a href="{{ route('tenant.register') }}" class="mt-8 inline-block bg-ink text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-slate-800 shadow-elevated transition-all active:scale-[0.98]">{{ __('landing.cta_final_button') }}</a>
            </x-landing.reveal>
        </div>
    </x-landing.scroll-3d-section>
</main>

<x-landing.footer />

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
