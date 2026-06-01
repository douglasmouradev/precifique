<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MonthlyGoal;
use App\Models\Tenant;

class MonthlyGoalPolicy
{
    public function update(Tenant $tenant, MonthlyGoal $goal): bool
    {
        return $goal->tenant_id === $tenant->id;
    }
}
