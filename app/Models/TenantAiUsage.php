<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantAiUsage extends Model
{
    protected $fillable = ['tenant_id', 'usage_date', 'requests'];

    protected function casts(): array
    {
        return [
            'usage_date' => 'date',
            'requests' => 'integer',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
