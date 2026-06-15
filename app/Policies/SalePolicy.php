<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sale;
use App\Models\Tenant;

class SalePolicy
{
    public function delete(Tenant $tenant, Sale $sale): bool
    {
        return $sale->tenant_id === $tenant->id;
    }

    public function update(Tenant $tenant, Sale $sale): bool
    {
        return $sale->tenant_id === $tenant->id;
    }
}
