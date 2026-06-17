<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', __('landing.meta_description'))">
    <meta name="keywords" content="precificação, MEI, custos, margem de lucro, ficha técnica, pequenos negócios">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'pt_BR' }}">
    <meta property="og:site_name" content="Precifique">
    <meta property="og:title" content="@yield('og_title', 'Precifique — Precificação inteligente')">
    <meta property="og:description" content="@yield('meta_description', 'Pare de chutar preços. Calcule custos, margem e lucro com confiança.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-precifique.svg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Precifique — Precificação inteligente')">
    <meta name="twitter:description" content="@yield('meta_description', 'Pare de chutar preços. Calcule custos, margem e lucro com confiança.')">
    <meta name="twitter:image" content="{{ asset('images/og-precifique.svg') }}">
    <x-head-icons />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#00C896">
    <title>@yield('title', 'Precifique')</title>
    <link rel="preconnect" href="{{ config('app.url') }}" crossorigin>
    @php $cspNonce = request()->attributes->get('csp_nonce'); @endphp
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
    <style @if(is_string($cspNonce) && $cspNonce !== '') nonce="{{ $cspNonce }}" @endif>
        [data-scroll-3d-hero] { background-color: #0D0D0D; min-height: 70vh; }
        .scroll-reveal { opacity: 1; transform: none; }
        html.landing-intro-seen #landing-intro-overlay { display: none !important; }
        #landing-intro-overlay { background-color: #0D0D0D; }
        html.cookies-accepted #landing-cookie-banner { display: none !important; }
    </style>
    @if(is_string($cspNonce) && $cspNonce !== '')
    <script nonce="{{ $cspNonce }}">
        try {
            if (localStorage.getItem('precifique_cookies') === '1') {
                document.documentElement.classList.add('cookies-accepted');
            }
            var skipIntro = window.matchMedia('(max-width: 767px)').matches
                || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (skipIntro || sessionStorage.getItem('precifique_intro_seen')) {
                document.documentElement.classList.add('landing-intro-seen');
                if (skipIntro) {
                    try { sessionStorage.setItem('precifique_intro_seen', '1'); } catch (e) {}
                }
                document.addEventListener('DOMContentLoaded', function () {
                    var el = document.getElementById('landing-intro-overlay');
                    if (el) el.remove();
                    document.body.style.overflow = '';
                });
            }
        } catch (e) {}
        document.addEventListener('DOMContentLoaded', function () {
            var acceptBtn = document.getElementById('landing-cookie-accept');
            var banner = document.getElementById('landing-cookie-banner');
            if (!acceptBtn || !banner) return;
            acceptBtn.addEventListener('click', function (e) {
                e.preventDefault();
                try { localStorage.setItem('precifique_cookies', '1'); } catch (err) {}
                document.documentElement.classList.add('cookies-accepted');
                banner.remove();
            });
        });
    </script>
    @endif
    <x-analytics />
    @stack('head')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Precifique",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "offers": { "@type": "Offer", "price": "0", "priceCurrency": "BRL" },
        "description": "SaaS de precificação para pequenos negócios brasileiros."
    }
    </script>
</head>
<body class="landing-page bg-paper text-ink font-sans antialiased overflow-x-hidden">
    @yield('content')

    <div
        id="landing-cookie-banner"
        class="fixed bottom-0 inset-x-0 z-[120] bg-ink text-white p-4 shadow-2xl pointer-events-auto"
        role="dialog"
        aria-label="{{ __('landing.cookie_message') }}"
    >
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm">{{ __('landing.cookie_message') }} <a href="{{ route('privacy') }}" class="text-brand underline">{{ __('app.nav.privacy') }}</a></p>
            <button
                type="button"
                id="landing-cookie-accept"
                class="bg-brand hover:bg-brand-dark px-6 py-3 min-h-[2.75rem] rounded-lg font-semibold text-ink touch-manipulation shrink-0"
            >{{ __('landing.cookie_accept') }}</button>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
