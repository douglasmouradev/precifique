<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SystemAuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class SystemAuditService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function log(
        string $action,
        ?User $user = null,
        array $metadata = [],
        ?Request $request = null,
    ): SystemAuditLog {
        return SystemAuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'metadata' => $metadata,
            'ip_address' => $request?->ip() ?? '0.0.0.0',
            'created_at' => now(),
        ]);
    }
}
