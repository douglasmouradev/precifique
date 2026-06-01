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

    public function privacy(): View
    {
        return view('landing.privacy');
    }

    public function terms(): View
    {
        return view('landing.terms');
    }
}
