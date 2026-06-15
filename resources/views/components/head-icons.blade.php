@php
    $iconVersion = '2';
    $iconBase = asset('images/apple-touch-icon.png').'?v='.$iconVersion;
@endphp
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}?v={{ $iconVersion }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $iconBase }}">
<link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/icon-192.png') }}?v={{ $iconVersion }}">
<link rel="apple-touch-icon-precomposed" sizes="180x180" href="{{ $iconBase }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Precifique">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="mobile-web-app-capable" content="yes">
