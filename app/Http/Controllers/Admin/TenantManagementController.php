<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TenantWelcomeMail;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\LGPDService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantManagementController extends Controller
{
    public function create(): View
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'niche' => ['required', 'in:alimentos,servico,artesanato,outro'],
            'plan' => ['required', 'in:basic,premium'],
        ]);

        $password = Str::password(16);

        $tenant = Tenant::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $password,
            'niche' => $data['niche'],
            'interface_mode' => $data['niche'] === 'outro' ? 'artesanato' : $data['niche'],
            'plan' => $data['plan'],
            'onboarding_completed' => true,
            'profile_setup_completed' => true,
            'created_by' => Auth::id(),
            'trial_ends_at' => now()->addDays(14),
        ]);

        if ($data['plan'] === 'premium') {
            $plan = Plan::where('slug', 'premium')->first();
            if ($plan) {
                Subscription::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                ]);
            }
        }

        $token = Password::broker('tenants')->createToken($tenant);
        $resetUrl = route('tenant.password.reset', ['token' => $token, 'email' => $tenant->email]);

        Mail::to($tenant->email)->send(new TenantWelcomeMail($tenant, $resetUrl));

        $lgpd = app(LGPDService::class);
        $version = (string) config('lgpd.policy_version', '1.0');
        $lgpd->recordConsent($tenant, $request, 'terms', $version);
        $lgpd->recordConsent($tenant, $request, 'privacy', $version);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant criado e e-mail enviado.');
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load(['subscription.plan', 'lgpdConsents' => fn ($q) => $q->latest('consented_at')->limit(5)]);

        return view('admin.tenants.show', compact('tenant'));
    }

    public function toggle(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);

        return back()->with('success', 'Status atualizado.');
    }
}
