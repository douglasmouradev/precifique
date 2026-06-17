<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TenantMember;
use App\Services\TotpService;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class TenantSecurityTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_viewer_cannot_create_product(): void
    {
        $tenant = $this->readyTenant(['email' => 'owner@precifique.com.br', 'password' => 'demo1234']);

        TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Visualizador',
            'email' => 'viewer@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'viewer',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'viewer@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->get(route('tenant.products.create'))->assertForbidden();

        $this->post(route('tenant.products.store'), [
            'name' => 'Novo produto',
            'niche_type' => 'alimentos',
            'is_active' => true,
        ])->assertForbidden();
    }

    public function test_viewer_cannot_create_sale(): void
    {
        $tenant = $this->readyTenant(['email' => 'owner3@precifique.com.br', 'password' => 'demo1234']);

        $product = $tenant->products()->create([
            'name' => 'Produto',
            'niche_type' => 'alimentos',
            'is_active' => true,
            'selling_price' => 10,
        ]);

        TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Visualizador',
            'email' => 'viewer2@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'viewer',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'viewer2@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->post(route('tenant.sales.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10,
            'payment_method' => 'pix',
        ])->assertForbidden();
    }

    public function test_editor_cannot_create_api_token(): void
    {
        $tenant = $this->readyTenant(['email' => 'owner4@precifique.com.br', 'password' => 'demo1234']);

        TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Editor',
            'email' => 'editor@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'editor@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->post(route('tenant.account.tokens.store'), [
            'name' => 'Token teste',
        ])->assertForbidden();
    }

    public function test_viewer_cannot_export_lgpd_data(): void
    {
        $tenant = $this->readyTenant(['email' => 'owner5@precifique.com.br', 'password' => 'demo1234']);

        TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Visualizador',
            'email' => 'viewer3@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'viewer',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'viewer3@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->get(route('tenant.lgpd.export'))->assertForbidden();
    }

    public function test_webhook_rejects_internal_url(): void
    {
        $this->readyTenant(['email' => 'owner6@precifique.com.br', 'password' => 'demo1234']);

        $this->post('/entrar', [
            'email' => 'owner6@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->post(route('tenant.account.webhooks.store'), [
            'url' => 'https://127.0.0.1/hook',
        ])->assertSessionHasErrors('url');
    }

    public function test_member_with_tenant_2fa_must_complete_challenge(): void
    {
        $secret = app(TotpService::class)->generateSecret();
        $tenant = $this->readyTenant([
            'email' => 'owner7@precifique.com.br',
            'password' => 'demo1234',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Editor 2FA',
            'email' => 'editor2fa@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'editor2fa@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.two-factor.challenge'));

        $code = app(TotpService::class)->getCode($secret);

        $this->post('/auth/2fa', ['code' => $code])
            ->assertRedirect(route('tenant.dashboard'));

        $this->get(route('tenant.dashboard'))->assertOk();
    }
}
