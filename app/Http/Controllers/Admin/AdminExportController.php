<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExportController extends Controller
{
    public function tenants(): StreamedResponse
    {
        $filename = 'tenants-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'name', 'email', 'plan', 'active', 'trial_ends_at', 'created_at']);

            Tenant::query()
                ->orderBy('id')
                ->chunk(200, function ($tenants) use ($out): void {
                    foreach ($tenants as $tenant) {
                        fputcsv($out, [
                            $tenant->id,
                            $tenant->name,
                            $tenant->email,
                            $tenant->plan?->value ?? $tenant->plan,
                            $tenant->is_active ? '1' : '0',
                            $tenant->trial_ends_at?->toDateTimeString() ?? '',
                            $tenant->created_at?->toDateTimeString() ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function auditLogs(): StreamedResponse
    {
        $filename = 'audit-logs-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'tenant', 'action', 'ip', 'created_at']);

            AuditLog::with('tenant')
                ->latest('created_at')
                ->limit(5000)
                ->chunk(200, function ($logs) use ($out): void {
                    foreach ($logs as $log) {
                        fputcsv($out, [
                            $log->id,
                            $log->tenant?->name ?? '',
                            $log->action,
                            $log->ip_address,
                            $log->created_at?->toDateTimeString() ?? '',
                        ]);
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
