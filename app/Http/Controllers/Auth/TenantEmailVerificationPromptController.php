<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantEmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        $tenant = $request->user('tenant');

        return $tenant?->hasVerifiedEmail()
            ? redirect()->intended(route('tenant.dashboard'))
            : view('auth.tenant-verify-email');
    }
}
