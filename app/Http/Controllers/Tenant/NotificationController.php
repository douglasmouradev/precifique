<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantNotification;
use App\Services\TenantNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationController extends Controller
{
    public function __construct(
        private readonly TenantNotificationService $notifications,
    ) {}

    public function index(): JsonResponse
    {
        $tenant = $this->resolveTenant();
        abort_unless($tenant, 401);

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

    public function stream(): StreamedResponse
    {
        $tenant = $this->resolveTenant();
        abort_unless($tenant, 401);

        return response()->stream(function () use ($tenant): void {
            for ($i = 0; $i < 6; $i++) {
                if (connection_aborted()) {
                    break;
                }

                echo 'data: '.json_encode([
                    'unread_count' => $this->notifications->unreadCount($tenant),
                ])."\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
                sleep(10);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function markRead(TenantNotification $notification): JsonResponse
    {
        $tenant = $this->resolveTenant();
        abort_unless($tenant && $notification->tenant_id === $tenant->id, 403);

        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function markAllRead(): RedirectResponse|JsonResponse
    {
        $tenant = $this->resolveTenant();
        abort_unless($tenant, 401);

        $this->notifications->markAllRead($tenant);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    private function resolveTenant(): ?Tenant
    {
        $tenant = Auth::guard('tenant')->user();

        return $tenant instanceof Tenant
            ? $tenant
            : Auth::guard('tenant_member')->user()?->tenant;
    }
}
