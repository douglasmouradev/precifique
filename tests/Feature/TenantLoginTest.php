<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class TenantLoginTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_can_login_with_password(): void
    {
        $tenant = $this->readyTenant(['email' => 'loja@test.com', 'password' => 'demo1234']);

        $response = $this->post('/entrar', [
            'email' => 'loja@test.com',
            'password' => 'demo1234',
        ]);

        $response->assertRedirect(route('tenant.dashboard'));
        $this->assertAuthenticatedAs($tenant, 'tenant');
    }

    public function test_test_profile_skips_email_verification(): void
    {
        $tenant = $this->readyTenant([
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
            'email_verified_at' => null,
        ]);

        $response = $this->post('/entrar', [
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $response->assertRedirect(route('tenant.dashboard'));
        $this->assertAuthenticatedAs($tenant, 'tenant');
        $this->assertNotNull($tenant->fresh()->email_verified_at);
    }
}
