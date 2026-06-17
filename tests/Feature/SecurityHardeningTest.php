<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_demo_login_blocked_when_disabled(): void
    {
        config(['tenancy.demo_enabled' => false]);

        $this->readyTenant([
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->from('/entrar')
            ->post('/entrar', [
                'email' => 'demo@precifique.com.br',
                'password' => 'demo1234',
            ])
            ->assertRedirect('/entrar')
            ->assertSessionHasErrors('email');

        $this->assertGuest('tenant');
    }

    public function test_openapi_hidden_when_docs_disabled(): void
    {
        config(['security.public_api_docs' => false]);

        $this->get('/openapi.yaml')->assertNotFound();
        $this->get('/docs/api')->assertNotFound();
    }

    public function test_health_requires_token_outside_local(): void
    {
        $this->app['env'] = 'staging';
        config(['precifique.monitoring.health_token' => 'secret-token']);

        $this->getJson('/health')->assertForbidden();

        $this->getJson('/health', ['Authorization' => 'Bearer secret-token'])
            ->assertOk();
    }

    public function test_mercadopago_webhook_rejects_unsigned_in_staging(): void
    {
        $this->app['env'] = 'staging';
        config(['services.mercadopago.webhook_secret' => '']);

        $this->postJson('/webhooks/mercadopago', [
            'type' => 'other',
        ])->assertStatus(400);
    }

    public function test_registration_honeypot_rejects_bots(): void
    {
        $this->post('/cadastro', [
            'name' => 'Bot Loja',
            'email' => 'bot@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'niche' => 'alimentos',
            'company_website' => 'https://spam.test',
        ])->assertSessionHasErrors('company_website');

        $this->assertDatabaseMissing('tenants', ['email' => 'bot@example.com']);
    }
}
