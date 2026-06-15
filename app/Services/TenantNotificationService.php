<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantNotification;
use Illuminate\Support\Collection;

class TenantNotificationService
{
    public function notify(
        Tenant $tenant,
        string $type,
        string $title,
        ?string $body = null,
        ?string $actionUrl = null,
    ): TenantNotification {
        return $tenant->notifications()->create([
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
        ]);
    }

    /** @return Collection<int, TenantNotification> */
    public function unread(Tenant $tenant, int $limit = 10): Collection
    {
        return $tenant->notifications()
            ->whereNull('read_at')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function unreadCount(Tenant $tenant): int
    {
        return $tenant->notifications()->whereNull('read_at')->count();
    }

    public function markAllRead(Tenant $tenant): int
    {
        return $tenant->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
