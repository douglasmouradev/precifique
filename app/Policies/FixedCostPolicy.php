<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FixedCost;
use App\Models\Tenant;

class FixedCostPolicy
{
    public function update(Tenant $tenant, FixedCost $fixedCost): bool
    {
        return $fixedCost->tenant_id === $tenant->id;
    }

    public function delete(Tenant $tenant, FixedCost $fixedCost): bool
    {
        return $fixedCost->tenant_id === $tenant->id;
    }
}
