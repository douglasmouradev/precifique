<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class UnifiedLoginTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_can_login_via_entrar(): void
    {
        $tenant = $this->readyTenant(['email' => 'demo@precifique.com.br', 'password' => 'demo1234']);

        $this->post('/entrar', [
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
        ])
            ->assertRedirect(route('tenant.dashboard'));

        $this->assertAuthenticatedAs($tenant, 'tenant');
    }

    public function test_tenant_can_login_via_login_route(): void
    {
        $tenant = $this->readyTenant(['email' => 'demo@precifique.com.br', 'password' => 'demo1234']);

        $this->post('/login', [
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
        ])
            ->assertRedirect(route('tenant.dashboard'));

        $this->assertAuthenticatedAs($tenant, 'tenant');
    }

    public function test_admin_can_login_via_entrar(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
            'is_superadmin' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
        ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_page_redirects_to_entrar(): void
    {
        $this->get('/login')->assertRedirect(route('tenant.login'));
    }

    public function test_wrong_password_shows_generic_error(): void
    {
        User::factory()->create([
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
            'is_superadmin' => true,
        ]);

        $this->from('/entrar')
            ->post('/entrar', [
                'email' => 'admin@precifique.com.br',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/entrar')
            ->assertSessionHasErrors([
                'email' => __('auth.failed'),
            ]);
    }
}
