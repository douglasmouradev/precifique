<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tenant;
use App\Models\TenantVariableCost;

class TenantVariableCostPolicy
{
    public function update(Tenant $tenant, TenantVariableCost $cost): bool
    {
        return $cost->tenant_id === $tenant->id;
    }

    public function delete(Tenant $tenant, TenantVariableCost $cost): bool
    {
        return $cost->tenant_id === $tenant->id;
    }
}
