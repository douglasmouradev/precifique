<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Precifique — calcule custos reais, margem de lucro e preço de venda para alimentos, serviços e artesanato. Trial Premium grátis.')">
    <meta name="keywords" content="precificação, MEI, custos, margem de lucro, ficha técnica, pequenos negócios">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
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
    <title>@yield('title', 'Precifique')</title>
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
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
<body class="landing-page bg-paper text-ink font-sans antialiased overflow-x-hidden" x-data="{ cookieAccepted: localStorage.getItem('precifique_cookies') === '1' }">
    @yield('content')

    <div x-show="!cookieAccepted" x-cloak class="fixed bottom-0 inset-x-0 z-50 bg-ink text-white p-4 shadow-2xl">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm">Usamos cookies para melhorar sua experiência. <a href="{{ route('privacy') }}" class="text-brand underline">Política de Privacidade</a></p>
            <button @click="localStorage.setItem('precifique_cookies','1'); cookieAccepted=true" class="bg-brand hover:bg-brand-dark px-6 py-2 rounded-lg font-semibold text-ink">Aceitar</button>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
