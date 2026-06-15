<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantEmailVerificationController extends Controller
{
    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $tenant = $request->user('tenant') ?? Tenant::findOrFail($id);

        if (! hash_equals((string) $id, (string) $tenant->getKey())) {
            abort(403);
        }

        if (! hash_equals($hash, sha1($tenant->getEmailForVerification()))) {
            abort(403);
        }

        if ($tenant->hasVerifiedEmail()) {
            return redirect()->route('tenant.dashboard');
        }

        if ($tenant->markEmailAsVerified()) {
            event(new Verified($tenant));
        }

        if (! $request->user('tenant')) {
            auth('tenant')->login($tenant);
        }

        return redirect()->route('tenant.dashboard')->with('success', __('Email verified successfully.'));
    }

    public function send(Request $request): RedirectResponse
    {
        $tenant = $request->user('tenant');

        if ($tenant->hasVerifiedEmail()) {
            return back();
        }

        $tenant->sendEmailVerificationNotification();

        return back()->with('success', __('Verification link sent.'));
    }
}
