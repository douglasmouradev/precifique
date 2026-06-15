<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantNotification;
use App\Services\TenantNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private readonly TenantNotificationService $notifications,
    ) {}

    public function index(): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();

        $items = $tenant->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (TenantNotification $n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'body' => $n->body,
                'action_url' => $n->action_url,
                'read_at' => $n->read_at?->toIso8601String(),
                'created_at' => $n->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'unread_count' => $this->notifications->unreadCount($tenant),
            'notifications' => $items,
        ]);
    }

    public function markRead(TenantNotification $notification): JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();
        abort_unless($notification->tenant_id === $tenant->id, 403);

        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function markAllRead(): RedirectResponse|JsonResponse
    {
        $tenant = Auth::guard('tenant')->user();
        $this->notifications->markAllRead($tenant);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}
