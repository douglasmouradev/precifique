<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function stop(): RedirectResponse
    {
        $adminId = session()->pull('impersonating_from_admin');
        $tenantId = session()->pull('impersonating_tenant_id');
        $tenant = $tenantId ? Tenant::find($tenantId) : null;

        Auth::guard('tenant')->logout();

        if ($tenant && $adminId) {
            $this->audit->logAdminForTenant($tenant, (int) $adminId, 'admin.impersonate.stop', [], request());
        }

        if ($adminId && $admin = User::find($adminId)) {
            Auth::guard('web')->login($admin);

            if ($tenantId) {
                return redirect()->route('admin.tenants.show', $tenantId)
                    ->with('success', 'Você saiu da conta do cliente.');
            }

            return redirect()->route('admin.tenants.index')
                ->with('success', 'Você saiu da conta do cliente.');
        }

        return redirect()->route('tenant.login');
    }
}
