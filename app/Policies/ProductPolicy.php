<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\Tenant;

class ProductPolicy
{
    public function view(Tenant $tenant, Product $product): bool
    {
        return $product->tenant_id === $tenant->id;
    }

    public function update(Tenant $tenant, Product $product): bool
    {
        return $product->tenant_id === $tenant->id;
    }

    public function delete(Tenant $tenant, Product $product): bool
    {
        return $product->tenant_id === $tenant->id;
    }
}
