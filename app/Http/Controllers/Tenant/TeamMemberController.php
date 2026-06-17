<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeamMemberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->ownerTenant();
        abort_unless($tenant, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('tenant_members', 'email')->where('tenant_id', $tenant->id)],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'editor', 'viewer'])],
        ]);

        $tenant->members()->create($data);

        return back()->with('success', __('members.invite'));
    }

    public function destroy(TenantMember $member): RedirectResponse
    {
        $tenant = $this->ownerTenant();
        abort_unless($tenant && $member->tenant_id === $tenant->id, 403);

        $member->delete();

        return back()->with('success', __('members.removed'));
    }

    private function ownerTenant()
    {
        if (Auth::guard('tenant')->check()) {
            return current_tenant();
        }

        $member = Auth::guard('tenant_member')->user();

        return $member?->canManageMembers() ? $member->tenant : null;
    }
}
