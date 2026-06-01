<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaborCost extends Model
{
    use BelongsToTenant;

    protected $fillable = ['product_id', 'tenant_id', 'hourly_rate', 'hours_spent'];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'hours_spent' => 'decimal:2',
            'total_labor' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
