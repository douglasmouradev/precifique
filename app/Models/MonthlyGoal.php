<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyGoal extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'year', 'month', 'goal_amount'];

    protected function casts(): array
    {
        return ['goal_amount' => 'decimal:2'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
