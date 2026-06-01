<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function log(
        Tenant $tenant,
        string $action,
        ?Model $entity = null,
        array $metadata = [],
        ?Request $request = null,
    ): AuditLog {
        return AuditLog::create([
            'tenant_id' => $tenant->id,
            'action' => $action,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity?->getKey(),
            'metadata' => $metadata,
            'ip_address' => $request?->ip() ?? '0.0.0.0',
            'created_at' => now(),
        ]);
    }
}
