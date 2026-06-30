<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Illuminate\Testing\TestResponse;

trait BuildsStripeWebhookSignature
{
    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function stripeWebhookPayload(array $overrides = []): string
    {
        $payload = array_replace_recursive([
            'id' => 'evt_test_'.uniqid(),
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'object' => 'checkout.session',
                    'metadata' => [
                        'tenant_id' => '1',
                        'plan_id' => '2',
                    ],
                    'subscription' => 'sub_test_123',
                ],
            ],
        ], $overrides);

        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    protected function stripeWebhookSignature(string $payload, string $secret): string
    {
        $timestamp = time();
        $signedPayload = "{$timestamp}.{$payload}";
        $signature = hash_hmac('sha256', $signedPayload, $secret);

        return "t={$timestamp},v1={$signature}";
    }

    protected function postStripeWebhook(string $payload, string $secret = 'whsec_test_secret'): TestResponse
    {
        config(['services.stripe.webhook_secret' => $secret]);

        return $this->call('POST', '/webhooks/stripe', [], [], [], [
            'HTTP_Stripe-Signature' => $this->stripeWebhookSignature($payload, $secret),
            'CONTENT_TYPE' => 'application/json',
        ], $payload);
    }
}
