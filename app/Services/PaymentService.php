<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\Billing\MercadoPagoBillingService;
use App\Services\Billing\StripeBillingService;
use App\Services\Billing\SubscriptionLifecycleService;
use Illuminate\Http\Request;

/**
 * Fachada de billing — delega para serviços por provedor.
 */
class PaymentService
{
    public function __construct(
        private readonly StripeBillingService $stripe,
        private readonly MercadoPagoBillingService $mercadopago,
        private readonly SubscriptionLifecycleService $subscriptions,
    ) {}

    public function createStripeCheckout(Tenant $tenant, Plan $plan): string
    {
        return $this->stripe->createCheckout($tenant, $plan);
    }

    public function verifyStripeSession(string $sessionId, int $expectedTenantId): bool
    {
        return $this->isStripeSessionPaid($sessionId, $expectedTenantId);
    }

    public function isStripeSessionPaid(string $sessionId, int $expectedTenantId): bool
    {
        return $this->stripe->isSessionPaid($sessionId, $expectedTenantId);
    }

    /**
     * @return array<string, mixed>
     */
    public function getOrCreateMercadoPagoPix(Tenant $tenant, Plan $plan): array
    {
        return $this->mercadopago->getOrCreatePix($tenant, $plan);
    }

    /**
     * @return array<string, mixed>
     */
    public function createMercadoPagoPix(Tenant $tenant, Plan $plan): array
    {
        return $this->mercadopago->createPix($tenant, $plan);
    }

    public function clearPendingPixCache(int $tenantId): void
    {
        $this->subscriptions->clearPendingPixCache($tenantId);
    }

    public function billingPortalUrl(Tenant $tenant): ?string
    {
        $stripeUrl = $this->createStripePortalSession($tenant);
        if ($stripeUrl !== null) {
            return $stripeUrl;
        }

        $subscription = Subscription::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->whereNotNull('mercadopago_payment_id')
            ->first();

        if ($subscription) {
            return route('tenant.billing.pix');
        }

        return null;
    }

    public function createStripePortalSession(Tenant $tenant): ?string
    {
        return $this->stripe->createPortalSession($tenant);
    }

    public function handleStripeWebhook(Request $request): bool
    {
        return $this->stripe->handleWebhook($request);
    }

    public function handleMercadoPagoWebhook(Request $request): bool
    {
        return $this->mercadopago->handleWebhook($request);
    }

    public function expireSubscriptions(): int
    {
        return $this->subscriptions->expireSubscriptions();
    }
}
