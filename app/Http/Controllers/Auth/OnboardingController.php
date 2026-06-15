<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OnboardingCompleteRequest;
use App\Http\Requests\Auth\OnboardingNicheRequest;
use App\Http\Requests\Auth\OnboardingPlanRequest;
use App\Models\FixedCost;
use App\Models\Plan;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function __construct(
        private readonly ImageUploadService $images,
    ) {}

    public function welcome(): RedirectResponse|View
    {
        $tenant = Auth::guard('tenant')->user();

        if ($tenant?->niche) {
            return redirect()->route('onboarding.mode');
        }

        return view('auth.onboarding.welcome');
    }

    public function niche(): View
    {
        return view('auth.onboarding.niche');
    }

    public function saveNiche(OnboardingNicheRequest $request): RedirectResponse
    {
        Auth::guard('tenant')->user()->update($request->nicheAttributes());

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

    public function savePlan(OnboardingPlanRequest $request): RedirectResponse
    {
        $request->session()->put('onboarding_selected_plan', $request->validated('plan'));
        Auth::guard('tenant')->user()->update(['plan' => 'basic']);

        return redirect()->route('onboarding.setup');
    }

    public function setup(): View
    {
        return view('auth.onboarding.setup');
    }

    public function complete(OnboardingCompleteRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $tenant->logo_path = $this->images->storeLogo($request->file('logo'), $tenant->id);
        }

        $tenant->name = $data['name'];
        $tenant->profile_setup_completed = true;
        $tenant->onboarding_completed = true;
        $tenant->save();

        FixedCost::create([
            'tenant_id' => $tenant->id,
            'name' => $data['fixed_cost_name'],
            'amount' => $data['fixed_cost_amount'],
        ]);

        if ($request->session()->pull('onboarding_selected_plan') === 'premium') {
            return redirect()->route('tenant.billing.upgrade')
                ->with('success', __('messages.onboarding.account_ready_premium'));
        }

        return redirect()->route('tenant.dashboard')
            ->with('success', __('messages.onboarding.account_created'))
            ->with('guided_setup', true);
    }
}
