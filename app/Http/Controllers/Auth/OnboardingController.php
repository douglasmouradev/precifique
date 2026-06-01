<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\FixedCost;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function welcome(): View
    {
        return view('auth.onboarding.welcome');
    }

    public function niche(): View
    {
        return view('auth.onboarding.niche');
    }

    public function saveNiche(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
            'niche_other' => ['nullable', 'string', 'max:255'],
        ]);

        $tenant = Auth::guard('tenant')->user();
        $interface = $data['niche'] === 'outro' ? 'artesanato' : $data['niche'];

        $tenant->update([
            'niche' => $data['niche'],
            'interface_mode' => $interface,
            'niche_metadata' => $data['niche_other'] ? ['other' => $data['niche_other']] : null,
        ]);

        return redirect()->route('onboarding.mode');
    }

    public function mode(): View
    {
        return view('auth.onboarding.mode');
    }

    public function saveMode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'usage_mode' => ['required', 'in:iniciante,avancado'],
        ]);

        Auth::guard('tenant')->user()->update(['usage_mode' => $data['usage_mode']]);

        return redirect()->route('onboarding.plan');
    }

    public function skip(): RedirectResponse
    {
        Auth::guard('tenant')->user()->update(['usage_mode' => 'avancado']);

        return redirect()->route('onboarding.plan');
    }

    public function plan(): View
    {
        return view('auth.onboarding.plan', [
            'plans' => Plan::query()->where('is_active', true)->orderBy('price_monthly')->get(),
        ]);
    }

    public function savePlan(Request $request): RedirectResponse
    {
        $data = $request->validate(['plan' => ['required', 'in:basic,premium']]);
        $request->session()->put('onboarding_selected_plan', $data['plan']);
        Auth::guard('tenant')->user()->update(['plan' => 'basic']);

        return redirect()->route('onboarding.setup');
    }

    public function setup(): View
    {
        return view('auth.onboarding.setup');
    }

    public function complete(Request $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'fixed_cost_name' => ['required', 'string', 'max:255'],
            'fixed_cost_amount' => ['required', 'numeric', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos/'.$tenant->id, 'public');
            $tenant->logo_path = $path;
        }

        $tenant->name = $data['name'];
        $tenant->onboarding_completed = true;
        $tenant->save();

        FixedCost::create([
            'tenant_id' => $tenant->id,
            'name' => $data['fixed_cost_name'],
            'amount' => $data['fixed_cost_amount'],
        ]);

        $request->session()->put('guided_setup', true);

        return redirect()->route('lgpd.consent')
            ->with('success', 'Conta criada! Aceite os termos para continuar.');
    }
}
