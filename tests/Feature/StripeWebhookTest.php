<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_missing_secret(): void
    {
        config(['services.stripe.webhook_secret' => '']);

        $this->post('/webhooks/stripe', [], [
            'Stripe-Signature' => 't=1,v1=abc',
        ])->assertStatus(400);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test_secret']);

        $this->call('POST', '/webhooks/stripe', [], [], [], [
            'HTTP_Stripe-Signature' => 't=1,v1=invalid',
            'CONTENT_TYPE' => 'application/json',
        ], '{"id":"evt_1"}')
            ->assertStatus(400);
    }
}
