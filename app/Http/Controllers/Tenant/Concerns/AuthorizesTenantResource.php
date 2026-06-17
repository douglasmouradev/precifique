<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant\Concerns;

use Illuminate\Support\Facades\Gate;

trait AuthorizesTenantResource
{
    protected function authorizeTenant(string $ability, mixed $resource): void
    {
        if (! tenant_member_can($ability)) {
            abort(403);
        }

        $tenant = current_tenant();
        abort_unless($tenant, 403);

        Gate::forUser($tenant)->authorize($ability, $resource);
    }
}
