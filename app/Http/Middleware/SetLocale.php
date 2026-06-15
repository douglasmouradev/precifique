<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var list<string> */
    public const SUPPORTED = ['pt_BR', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        App::setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        if ($request->hasSession() && $request->session()->has('locale')) {
            $session = (string) $request->session()->get('locale');
            if ($this->isSupported($session)) {
                return $session;
            }
        }

        $tenant = Auth::guard('tenant')->user();
        if ($tenant && $this->isSupported((string) ($tenant->locale ?? ''))) {
            return (string) $tenant->locale;
        }

        $cookie = $request->cookie('locale');
        if (is_string($cookie) && $this->isSupported($cookie)) {
            return $cookie;
        }

        $preferred = $request->getPreferredLanguage(['pt_BR', 'pt', 'en']);
        if ($preferred !== null && str_starts_with($preferred, 'en')) {
            return 'en';
        }

        return config('app.locale', 'pt_BR');
    }

    private function isSupported(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED, true);
    }
}
