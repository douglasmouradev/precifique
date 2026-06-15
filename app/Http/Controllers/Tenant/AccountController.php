<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateAccountPasswordRequest;
use App\Http\Requests\Tenant\UpdateAccountProfileRequest;
use App\Models\Subscription;
use App\Models\TenantApiToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $tenant = Auth::guard('tenant')->user();
        $subscription = Subscription::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->first();

        return view('tenant.account', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'apiTokensCount' => TenantApiToken::query()->where('tenant_id', $tenant->id)->count(),
        ]);
    }

    public function updateProfile(UpdateAccountProfileRequest $request): RedirectResponse
    {
        Auth::guard('tenant')->user()->update($request->profileAttributes());

        return back()->with('success', 'Perfil atualizado.');
    }

    public function updatePassword(UpdateAccountPasswordRequest $request): RedirectResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $tenant->update(['password' => $request->validated('password')]);

        return back()->with('success', 'Senha alterada com sucesso.');
    }
}
