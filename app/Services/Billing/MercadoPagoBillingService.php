<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\Tenant;
use App\Services\WebhookIdempotencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoBillingService
{
    public function __construct(
        private readonly WebhookIdempotencyService $webhookIdempotency,
        private readonly SubscriptionLifecycleService $subscriptions,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getOrCreatePix(Tenant $tenant, Plan $plan): array
    {
        if ($tenant->isPremium()) {
            return ['error' => __('messages.billing.already_premium')];
        }

        $cacheKey = "pix_checkout:{$tenant->id}";
        $ttlMinutes = (int) config('precifique.pix.pending_ttl_minutes', 30);
        $cached = Cache::get($cacheKey);

        if (is_array($cached) && ! isset($cached['error']) && ($cached['created_at'] ?? 0) >= now()->subMinutes($ttlMinutes)->timestamp) {
            return $cached;
        }

        $result = $this->createPix($tenant, $plan);

        if (! isset($result['error'])) {
            $result['created_at'] = now()->timestamp;
            Cache::put($cacheKey, $result, now()->addMinutes($ttlMinutes));
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function createPix(Tenant $tenant, Plan $plan): array
    {
        $accessToken = (string) config('services.mercadopago.access_token', '');

        if ($accessToken === '') {
            return ['error' => __('messages.billing.mercadopago_not_configured')];
        }

        $response = Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => (float) $plan->price_monthly,
                'description' => __('messages.billing.pix_description', ['name' => $tenant->name]),
                'payment_method_id' => 'pix',
                'payer' => ['email' => $tenant->email],
                'external_reference' => "tenant:{$tenant->id}:plan:{$plan->id}",
            ]);

        if (! $response->successful()) {
            Log::warning('Mercado Pago PIX error', ['body' => $response->body()]);

            return ['error' => __('messages.billing.pix_generation_failed')];
        }

        $data = $response->json();

        return [
            'payment_id' => $data['id'] ?? null,
            'qr_code' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
        ];
    }

    public function handleWebhook(Request $request): bool
    {
        if (! $this->validateSignature($request)) {
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
            return $this->subscriptions->activatePremium(
                (int) $matches[1],
                (int) $matches[2],
                null,
                (string) $paymentId,
                null,
            );
        });
    }

    private function validateSignature(Request $request): bool
    {
        $secret = (string) config('services.mercadopago.webhook_secret', '');

        if ($secret === '') {
            return app()->environment(['local', 'testing']);
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
