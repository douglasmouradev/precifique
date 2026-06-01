<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::with('tenant')
            ->latest('created_at')
            ->paginate(40);

        $aiLogs = AuditLog::where('action', 'like', 'ai.%')
            ->latest('created_at')
            ->limit(20)
            ->get();

        return view('admin.logs.index', compact('logs', 'aiLogs'));
    }
}
