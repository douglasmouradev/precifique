<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::orderBy('price_monthly')->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'max_products' => ['nullable', 'integer', 'min:1'],
            'has_ai' => ['boolean'],
            'is_active' => ['boolean'],
            'stripe_price_id' => ['nullable', 'string', 'max:255'],
        ]);

        $plan->update([
            'name' => $data['name'],
            'price_monthly' => $data['price_monthly'],
            'max_products' => $data['max_products'] ?? null,
            'has_ai' => $request->boolean('has_ai'),
            'is_active' => $request->boolean('is_active'),
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
        ]);

        return back()->with('success', 'Plano atualizado.');
    }
}
