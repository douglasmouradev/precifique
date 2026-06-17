<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\Concerns\AuthorizesTenantResource;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BillingController extends Controller
{
    use AuthorizesTenantResource;

    public function __construct(
        private readonly PaymentService $payments,
    ) {}

    public function upgrade(): View
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant()->load('subscription');
        $plan = Plan::where('slug', 'premium')->first();

        return view('billing.upgrade', compact('plan', 'tenant'));
    }

    public function stripeCheckout(): RedirectResponse
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant();
        $plan = Plan::where('slug', 'premium')->firstOrFail();
        $url = $this->payments->createStripeCheckout($tenant, $plan);

        return redirect()->away($url);
    }

    public function pix(): View
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant();
        $plan = Plan::where('slug', 'premium')->firstOrFail();
        $pix = $this->payments->getOrCreateMercadoPagoPix($tenant, $plan);

        return view('billing.pix', compact('pix', 'plan'));
    }

    public function success(Request $request): RedirectResponse
    {
        $this->authorizeTenantOwner();
        $sessionId = (string) $request->query('session_id', '');
        $tenant = current_tenant();

        if ($tenant?->isPremium()) {
            return redirect()->route('tenant.dashboard')->with('success', __('messages.billing.payment_confirmed'));
        }

        if ($sessionId !== '' && $tenant && $this->payments->isStripeSessionPaid($sessionId, $tenant->id)) {
            return redirect()->route('tenant.dashboard')->with('success', __('messages.billing.payment_received'));
        }

        return redirect()->route('tenant.billing.upgrade')->with('warning', __('messages.billing.payment_processing'));
    }

    public function pixStatus(): \Illuminate\Http\JsonResponse
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant()->fresh();

        return response()->json([
            'premium' => $tenant->isPremium(),
        ]);
    }

    public function cancel(): RedirectResponse
    {
        $this->authorizeTenantOwner();

        return redirect()->route('tenant.billing.upgrade')->with('warning', __('messages.billing.payment_cancelled'));
    }

    public function portal(): RedirectResponse
    {
        $this->authorizeTenantOwner();
        $tenant = current_tenant();
        $url = $this->payments->billingPortalUrl($tenant);

        if ($url === null) {
            return back()->with('warning', __('messages.billing.portal_unavailable'));
        }

        if (str_starts_with($url, 'http')) {
            return redirect()->away($url);
        }

        return redirect($url);
    }

    public function stripeWebhook(Request $request): Response
    {
        if (! $this->payments->handleStripeWebhook($request)) {
            return response('Invalid webhook', 400);
        }

        return response('OK', 200);
    }

    public function mercadopagoWebhook(Request $request): Response
    {
        if (! $this->payments->handleMercadoPagoWebhook($request)) {
            return response('Invalid webhook', 400);
        }

        return response('OK', 200);
    }
}
