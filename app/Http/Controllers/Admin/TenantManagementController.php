<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TenantWelcomeMail;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        $password = Str::password(12);

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

        Mail::to($tenant->email)->send(new TenantWelcomeMail($tenant, $password));

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant criado e e-mail enviado.');
    }

    public function toggle(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);

        return back()->with('success', 'Status atualizado.');
    }
}
