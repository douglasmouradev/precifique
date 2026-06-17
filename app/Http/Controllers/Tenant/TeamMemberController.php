<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\Concerns\AuthorizesTenantResource;
use App\Http\Controllers\Controller;
use App\Models\TenantMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class TeamMemberController extends Controller
{
    use AuthorizesTenantResource;

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeTenantManageAccount();
        $tenant = current_tenant();
        abort_unless($tenant, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('tenant_members', 'email')->where('tenant_id', $tenant->id)],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'editor', 'viewer'])],
        ]);

        $tenant->members()->create($data);

        return back()->with('success', __('members.invite'));
    }

    public function destroy(TenantMember $member): RedirectResponse
    {
        $this->authorizeTenantManageAccount();
        $tenant = current_tenant();
        abort_unless($tenant && $member->tenant_id === $tenant->id, 403);

        $member->delete();

        return back()->with('success', __('members.removed'));
    }
}
