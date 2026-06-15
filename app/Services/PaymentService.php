<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PlanType;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\BillingPortal\Session as StripePortalSession;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentService
{
    public function __construct(
        private readonly WebhookIdempotencyService $webhookIdempotency,
    ) {}

    public function createStripeCheckout(Tenant $tenant, Plan $plan): string
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));

        $session = StripeSession::create([
            'mode' => 'subscription',
            'customer_email' => $tenant->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => ['name' => "Precifique — {$plan->name}"],
                    'unit_amount' => (int) ($plan->price_monthly * 100),
                    'recurring' => ['interval' => 'month'],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'tenant_id' => (string) $tenant->id,
                'plan_id' => (string) $plan->id,
            ],
            'success_url' => route('tenant.billing.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('tenant.billing.cancel'),
        ]);

        return $session->url ?? route('tenant.dashboard');
    }

    public function verifyStripeSession(string $sessionId, int $expectedTenantId): bool
    {
        return $this->isStripeSessionPaid($sessionId, $expectedTenantId);
    }

    public function isStripeSessionPaid(string $sessionId, int $expectedTenantId): bool
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

    /**
     * @return array<string, mixed>
     */
    public function createMercadoPagoPix(Tenant $tenant, Plan $plan): array
    {
        $accessToken = (string) config('services.mercadopago.access_token', '');

        if ($accessToken === '') {
            return ['error' => 'Mercado Pago não configurado.'];
        }

        $response = Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => (float) $plan->price_monthly,
                'description' => "Precifique Premium — {$tenant->name}",
                'payment_method_id' => 'pix',
                'payer' => ['email' => $tenant->email],
                'external_reference' => "tenant:{$tenant->id}:plan:{$plan->id}",
            ]);

        if (! $response->successful()) {
            Log::warning('Mercado Pago PIX error', ['body' => $response->body()]);

            return ['error' => 'Não foi possível gerar o PIX.'];
        }

        $data = $response->json();

        return [
            'payment_id' => $data['id'] ?? null,
            'qr_code' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
        ];
    }

    public function createStripePortalSession(Tenant $tenant): ?string
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
                'customer' => $this->resolveStripeCustomerId($subscription->stripe_subscription_id),
                'return_url' => route('tenant.account.index'),
            ]);

            return $session->url ?? null;
        } catch (\Throwable $e) {
            Log::warning('Stripe portal failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    private function resolveStripeCustomerId(string $stripeSubscriptionId): string
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));
        $sub = \Stripe\Subscription::retrieve($stripeSubscriptionId);

        return (string) $sub->customer;
    }

    public function handleStripeWebhook(Request $request): bool
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
                'customer.subscription.deleted' => $this->handleStripeSubscriptionEnded($event->data->object->id ?? null),
                'customer.subscription.updated' => $this->handleStripeSubscriptionUpdated($event->data->object),
                'invoice.payment_failed' => true,
                default => true,
            };
        });
    }

    public function handleMercadoPagoWebhook(Request $request): bool
    {
        if (! $this->validateMercadoPagoSignature($request)) {
            Log::warning('Mercado Pago webhook rejected: invalid signature');

            return false;
        }

        $data = $request->all();
        $type = $data['type'] ?? $data['action'] ?? null;

        if ($type !== 'payment' && ($data['action'] ?? '') !== 'payment.updated') {
            return true;
        }

        $paymentId = $data['data']['id'] ?? null;
        if (! $paymentId) {
            return false;
        }

        $accessToken = (string) config('services.mercadopago.access_token', '');
        if ($accessToken === '') {
            Log::warning('Mercado Pago webhook rejected: missing access token');

            return false;
        }

        $response = Http::withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (! $response->successful()) {
            return false;
        }

        $payment = $response->json();
        if (($payment['status'] ?? '') !== 'approved') {
            return true;
        }

        $ref = (string) ($payment['external_reference'] ?? '');
        if (! preg_match('/tenant:(\d+):plan:(\d+)/', $ref, $matches)) {
            return false;
        }

        $eventId = 'mp.payment.'.(string) $paymentId;

        return $this->webhookIdempotency->processOnce('mercadopago', $eventId, function () use ($matches, $paymentId) {
            return $this->activatePremium(
                (int) $matches[1],
                (int) $matches[2],
                null,
                (string) $paymentId,
                null,
            );
        });
    }

    public function expireSubscriptions(): int
    {
        $expired = Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expired as $subscription) {
            $this->deactivatePremium($subscription);
        }

        return $expired->count();
    }

    private function handleCheckoutCompleted(object $session): bool
    {
        return $this->activatePremium(
            (int) ($session->metadata->tenant_id ?? 0),
            (int) ($session->metadata->plan_id ?? 0),
            $session->subscription ?? null,
            null,
            null,
        );
    }

    private function handleStripeSubscriptionUpdated(object $subscription): bool
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
            return $this->deactivatePremium($local);
        }

        $local->update(['status' => 'active', 'ends_at' => null]);

        return true;
    }

    private function handleStripeSubscriptionEnded(?string $stripeSubscriptionId): bool
    {
        if (! $stripeSubscriptionId) {
            return true;
        }

        $local = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
        if (! $local) {
            return true;
        }

        return $this->deactivatePremium($local);
    }

    private function activatePremium(
        int $tenantId,
        int $planId,
        ?string $stripeSubscriptionId,
        ?string $mercadopagoPaymentId,
        ?\DateTimeInterface $endsAt,
    ): bool {
        if (! $tenantId || ! $planId) {
            return false;
        }

        if ($mercadopagoPaymentId && Subscription::where('mercadopago_payment_id', $mercadopagoPaymentId)->exists()) {
            return true;
        }

        if ($stripeSubscriptionId && Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->where('status', 'active')->exists()) {
            $existing = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
            if ($existing && $existing->tenant_id === $tenantId) {
                return true;
            }
        }

        $tenant = Tenant::find($tenantId);
        $plan = Plan::find($planId);

        if (! $tenant || ! $plan) {
            return false;
        }

        $subscriptionEnds = $endsAt;
        if ($subscriptionEnds === null && $mercadopagoPaymentId) {
            $subscriptionEnds = now()->addDays((int) config('tenancy.pix_subscription_days', 30));
        }

        return DB::transaction(function () use ($tenant, $plan, $stripeSubscriptionId, $mercadopagoPaymentId, $subscriptionEnds) {
            $tenant->update(['plan' => PlanType::Premium]);

            Subscription::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'stripe_subscription_id' => $stripeSubscriptionId,
                    'mercadopago_payment_id' => $mercadopagoPaymentId,
                    'starts_at' => now(),
                    'ends_at' => $subscriptionEnds,
                ]
            );

            AdminMetricsService::forgetCache();

            return true;
        });
    }

    private function deactivatePremium(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => $subscription->ends_at ?? now(),
            ]);

            $tenant = $subscription->tenant;
            if (! $tenant) {
                return true;
            }

            if ($tenant->onTrial()) {
                return true;
            }

            $tenant->update(['plan' => PlanType::Basic]);
            AdminMetricsService::forgetCache();

            return true;
        });
    }

    private function validateMercadoPagoSignature(Request $request): bool
    {
        $secret = (string) config('services.mercadopago.webhook_secret', '');

        if ($secret === '') {
            return ! app()->environment('production');
        }

        $xSignature = (string) $request->header('x-signature', '');
        $xRequestId = (string) $request->header('x-request-id', '');
        $dataId = (string) ($request->input('data.id') ?? '');

        if ($xSignature === '' || $xRequestId === '' || $dataId === '') {
            return false;
        }

        $ts = null;
        $hash = null;
        foreach (explode(',', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);
            if ($key === 'ts') {
                $ts = $value;
            }
            if ($key === 'v1') {
                $hash = $value;
            }
        }

        if ($ts === null || $hash === null) {
            return false;
        }

        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $hash);
    }
}
