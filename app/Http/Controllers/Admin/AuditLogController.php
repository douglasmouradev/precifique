<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $action = trim((string) $request->input('action', ''));

        $logs = AuditLog::with('tenant')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhereHas('tenant', fn ($t) => $t->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($action !== '', fn ($query) => $query->where('action', 'like', "%{$action}%"))
            ->latest('created_at')
            ->paginate(40)
            ->withQueryString();

        $aiLogs = AuditLog::where('action', 'like', 'ai.%')
            ->when($search !== '', fn ($query) => $query->where('action', 'like', "%{$search}%"))
            ->latest('created_at')
            ->limit(20)
            ->get();

        $systemLogs = SystemAuditLog::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($action !== '', fn ($query) => $query->where('action', 'like', "%{$action}%"))
            ->latest('created_at')
            ->limit(30)
            ->get();

        return view('admin.logs.index', compact('logs', 'aiLogs', 'systemLogs', 'search', 'action'));
    }
}
