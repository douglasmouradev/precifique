<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FailedJobController extends Controller
{
    public function index(): View
    {
        $jobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->limit(50)
            ->get();

        return view('admin.failed-jobs.index', compact('jobs'));
    }

    public function retry(string $uuid): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => $uuid]);

        return back()->with('success', __('admin.failed_jobs.retried'));
    }

    public function retryAll(): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => 'all']);

        return back()->with('success', __('admin.failed_jobs.retried_all'));
    }
}
