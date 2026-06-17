<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Precifique</title>
    <x-head-icons />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans text-ink antialiased min-h-screen gradient-mesh flex items-center justify-center p-6">
    <div class="w-full max-w-md text-center animate-fade-in">
        <a href="{{ url('/') }}" class="inline-flex transition-opacity hover:opacity-90 mb-8">
            <x-ui.logo variant="full" size="lg" />
        </a>
        @yield('content')
    </div>
</body>
</html>
