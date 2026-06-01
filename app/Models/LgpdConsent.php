<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LgpdConsent extends Model
{
    protected $fillable = [
        'tenant_id',
        'consent_type',
        'consented_at',
        'ip_address',
        'user_agent',
        'version',
    ];

    protected function casts(): array
    {
        return ['consented_at' => 'datetime'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
