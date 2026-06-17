<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TenantWebhook;
use App\Models\WebhookDeliveryLog;
use App\Services\TenantWebhookDispatcher;
use App\Services\TotpService;
use App\Services\TwoFactorRecoveryService;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class RoadmapPremiumFeaturesTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_can_login_with_recovery_code(): void
    {
        $secret = app(TotpService::class)->generateSecret();
        $recovery = app(TwoFactorRecoveryService::class);
        $plain = $recovery->generateSet(1);

        $tenant = $this->readyTenant([
            'email' => 'recovery@precifique.com.br',
            'password' => 'demo1234',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);
        $recovery->store($tenant, $plain);

        $this->post('/entrar', [
            'email' => 'recovery@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.two-factor.challenge'));

        $this->post('/auth/2fa', ['recovery_code' => $plain[0]])
            ->assertRedirect(route('tenant.dashboard'));

        $this->assertAuthenticatedAs($tenant->fresh(), 'tenant');
    }

    public function test_webhook_dispatcher_logs_delivery_result(): void
    {
        Http::fake(['https://example.com/*' => Http::response('ok', 200)]);

        $tenant = $this->readyTenant();
        $hook = TenantWebhook::query()->create([
            'tenant_id' => $tenant->id,
            'url' => 'https://example.com/precifique-hook',
            'secret' => 'secret',
            'events' => ['sale.created'],
            'is_active' => true,
        ]);

        app(TenantWebhookDispatcher::class)->dispatch($tenant, 'sale.created', ['id' => 1]);

        $this->assertDatabaseHas('webhook_delivery_logs', [
            'tenant_webhook_id' => $hook->id,
            'event' => 'sale.created',
            'success' => true,
            'http_status' => 200,
        ]);

        $this->assertSame(1, WebhookDeliveryLog::query()->count());
    }
}
