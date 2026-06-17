<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Tenant;
use App\Services\TenantSetupProgressService;

trait ForgetsTenantSetupProgress
{
    protected function forgetTenantSetupProgress(?Tenant $tenant = null): void
    {
        $tenant ??= current_tenant();
        if ($tenant) {
            app(TenantSetupProgressService::class)->forget($tenant);
        }
    }
}
