<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MercadoPagoWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_invalid_signature_in_production(): void
    {
        $this->app['env'] = 'production';
        config(['services.mercadopago.webhook_secret' => 'test-secret']);

        $this->postJson('/webhooks/mercadopago', [
            'type' => 'payment',
            'data' => ['id' => '12345'],
        ], [
            'x-signature' => 'ts=1,v1=invalid',
            'x-request-id' => 'req-1',
        ])->assertStatus(400);
    }

    public function test_webhook_allows_unsigned_in_local(): void
    {
        config(['services.mercadopago.webhook_secret' => '']);
        config(['services.mercadopago.access_token' => '']);

        $this->postJson('/webhooks/mercadopago', [
            'type' => 'other',
        ])->assertOk();
    }
}
