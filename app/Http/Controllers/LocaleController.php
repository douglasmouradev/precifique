<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $locale = $request->validate([
            'locale' => ['required', 'in:'.implode(',', SetLocale::SUPPORTED)],
        ])['locale'];

        $request->session()->put('locale', $locale);

        $tenant = Auth::guard('tenant')->user();
        if ($tenant) {
            $tenant->update(['locale' => $locale]);
        }

        Cookie::queue(cookie('locale', $locale, 60 * 24 * 365));

        return back();
    }
}
