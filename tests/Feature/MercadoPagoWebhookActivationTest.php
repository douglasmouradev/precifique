<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Tenant;
use Database\Seeders\PlanSeeder;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class MercadoPagoWebhookActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_payment_webhook_activates_premium(): void
    {
        $this->seed(PlanSeeder::class);
        config([
            'services.mercadopago.webhook_secret' => '',
            'services.mercadopago.access_token' => 'test-token',
        ]);

        $tenant = Tenant::factory()->create(['plan' => 'basic']);
        $plan = Plan::where('slug', 'premium')->firstOrFail();

        Http::fake([
            'api.mercadopago.com/v1/payments/999' => Http::response([
                'id' => 999,
                'status' => 'approved',
                'external_reference' => "tenant:{$tenant->id}:plan:{$plan->id}",
            ]),
        ]);

        $this->postJson('/webhooks/mercadopago', [
            'type' => 'payment',
            'data' => ['id' => '999'],
        ])->assertOk();

        $this->assertTrue($tenant->fresh()->isPremium());
        $this->assertSame('999', (string) $tenant->fresh()->subscription?->mercadopago_payment_id);
    }
}
