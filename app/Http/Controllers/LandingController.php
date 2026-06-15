<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $plans = Plan::where('is_active', true)->orderBy('price_monthly')->get();

        return view('landing.index', compact('plans'));
    }

    public function nicheFood(): View
    {
        return $this->nichePage('food', 'landing.niche_food_title', 'landing.niche_food_desc');
    }

    public function nicheService(): View
    {
        return $this->nichePage('service', 'landing.niche_service_title', 'landing.niche_service_desc');
    }

    public function nicheCraft(): View
    {
        return $this->nichePage('craft', 'landing.niche_craft_title', 'landing.niche_craft_desc');
    }

    private function nichePage(string $niche, string $titleKey, string $descKey): View
    {
        $plans = Plan::where('is_active', true)->orderBy('price_monthly')->get();

        return view('landing.niche', [
            'niche' => $niche,
            'pageTitle' => __($titleKey),
            'pageDescription' => __($descKey),
            'plans' => $plans,
        ]);
    }

    public function privacy(): View
    {
        return view('landing.privacy');
    }

    public function terms(): View
    {
        return view('landing.terms');
    }
}
