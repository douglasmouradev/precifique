<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();
        $minutes = (int) config('security.admin_session_lifetime', 120);

        if ($user?->is_superadmin && $minutes > 0) {
            $last = (int) session('admin_last_activity_at', 0);
            $now = now()->timestamp;

            if ($last > 0 && ($now - $last) > ($minutes * 60)) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('tenant.login')
                    ->with('warning', __('auth.admin_session_expired'));
            }

            session(['admin_last_activity_at' => $now]);
        }

        return $next($request);
    }
}
