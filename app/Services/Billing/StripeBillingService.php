<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Mail\PaymentFailedMail;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\TenantNotificationPreferences;
use App\Services\TenantNotificationService;
use App\Services\WebhookIdempotencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\BillingPortal\Session as StripePortalSession;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeBillingService
{
    public function __construct(
        private readonly WebhookIdempotencyService $webhookIdempotency,
        private readonly SubscriptionLifecycleService $subscriptions,
        private readonly TenantNotificationService $notifications,
        private readonly TenantNotificationPreferences $notificationPreferences,
    ) {}

    public function createCheckout(Tenant $tenant, Plan $plan): string
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));

        $lineItem = $plan->stripe_price_id
            ? ['price' => $plan->stripe_price_id, 'quantity' => 1]
            : [
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => ['name' => "Precifique — {$plan->name}"],
                    'unit_amount' => (int) ($plan->price_monthly * 100),
                    'recurring' => ['interval' => 'month'],
                ],
                'quantity' => 1,
            ];

        $session = StripeSession::create([
            'mode' => 'subscription',
            'customer_email' => $tenant->email,
            'line_items' => [$lineItem],
            'metadata' => [
                'tenant_id' => (string) $tenant->id,
                'plan_id' => (string) $plan->id,
            ],
            'success_url' => route('tenant.billing.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('tenant.billing.cancel'),
        ]);

        return $session->url ?? route('tenant.dashboard');
    }

    public function isSessionPaid(string $sessionId, int $expectedTenantId): bool
    {
        $secret = (string) config('services.stripe.secret');
        if ($secret === '' || $sessionId === '' || $expectedTenantId <= 0) {
            return false;
        }

        try {
            Stripe::setApiKey($secret);
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return false;
            }

            $sessionTenantId = (int) ($session->metadata->tenant_id ?? 0);

            return $sessionTenantId === $expectedTenantId;
        } catch (\Throwable $e) {
            Log::warning('Stripe session verify failed', ['message' => $e->getMessage()]);

            return false;
        }
    }

    public function createPortalSession(Tenant $tenant): ?string
    {
        $secret = (string) config('services.stripe.secret');
        if ($secret === '') {
            return null;
        }

        $subscription = Subscription::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->whereNotNull('stripe_subscription_id')
            ->first();

        if (! $subscription?->stripe_subscription_id) {
            return null;
        }

        try {
            Stripe::setApiKey($secret);
            $session = StripePortalSession::create([
                'customer' => $this->resolveCustomerId($subscription->stripe_subscription_id),
                'return_url' => route('tenant.account.index'),
            ]);

            return $session->url ?? null;
        } catch (\Throwable $e) {
            Log::warning('Stripe portal failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    public function handleWebhook(Request $request): bool
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = (string) config('services.stripe.webhook_secret');

        if ($secret === '' || $sigHeader === null) {
            Log::warning('Stripe webhook rejected: missing secret or signature');

            return false;
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook invalid', ['message' => $e->getMessage()]);

            return false;
        }

        $eventId = (string) ($event->id ?? '');

        return $this->webhookIdempotency->processOnce('stripe', $eventId, function () use ($event) {
            return match ($event->type) {
                'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
                'customer.subscription.deleted' => $this->handleSubscriptionEnded($event->data->object->id ?? null),
                'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
                'invoice.payment_failed' => $this->handleInvoicePaymentFailed($event->data->object),
                default => true,
            };
        });
    }

    private function resolveCustomerId(string $stripeSubscriptionId): string
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));
        $sub = \Stripe\Subscription::retrieve($stripeSubscriptionId);

        return (string) $sub->customer;
    }

    private function handleCheckoutCompleted(object $session): bool
    {
        return $this->subscriptions->activatePremium(
            (int) ($session->metadata->tenant_id ?? 0),
            (int) ($session->metadata->plan_id ?? 0),
            $session->subscription ?? null,
            null,
            null,
        );
    }

    private function handleSubscriptionUpdated(object $subscription): bool
    {
        $stripeId = $subscription->id ?? null;
        if (! $stripeId) {
            return true;
        }

        $local = Subscription::where('stripe_subscription_id', $stripeId)->first();
        if (! $local) {
            return true;
        }

        $status = $subscription->status ?? 'active';
        if (in_array($status, ['canceled', 'unpaid'], true)) {
            return $this->subscriptions->deactivatePremium($local);
        }

        if ($status === 'past_due') {
            $local->update(['status' => 'past_due']);

            return true;
        }

        $local->update(['status' => 'active', 'ends_at' => null]);

        return true;
    }

    private function handleSubscriptionEnded(?string $stripeSubscriptionId): bool
    {
        if (! $stripeSubscriptionId) {
            return true;
        }

        $local = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
        if (! $local) {
            return true;
        }

        return $this->subscriptions->deactivatePremium($local);
    }

    public function handleInvoicePaymentFailed(object $invoice): bool
    {
        $stripeSubscriptionId = $invoice->subscription ?? null;
        if (! $stripeSubscriptionId) {
            return true;
        }

        $local = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->with('tenant')->first();
        $tenant = $local?->tenant;
        if (! $tenant) {
            return true;
        }

        if ($this->notificationPreferences->allowsEmail($tenant, 'email_payment_failed')) {
            Mail::to($tenant->email)->queue(new PaymentFailedMail($tenant));
        }

        $graceDays = (int) config('precifique.billing.grace_period_days', 7);
        $local->update([
            'status' => 'past_due',
            'ends_at' => $local->ends_at ?? now()->addDays($graceDays),
        ]);

        if ($this->notificationPreferences->allowsInApp($tenant)) {
            $this->notifications->notify(
                $tenant,
                'payment_failed',
                __('messages.billing.payment_failed_title'),
                __('messages.billing.payment_failed_body'),
                route('tenant.billing.portal'),
            );
        }

        return true;
    }
}
