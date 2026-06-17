<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTenantHintTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_admin_login_shows_hint_for_tenant_email(): void
    {
        $this->readyTenant(['email' => 'demo@precifique.com.br', 'password' => 'demo1234']);

        $this->from('/login')
            ->post('/login', [
                'email' => 'demo@precifique.com.br',
                'password' => 'demo1234',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => __('auth.tenant_login_hint'),
            ]);
    }

    public function test_tenant_login_shows_hint_for_admin_email(): void
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
                'email' => __('auth.admin_login_hint'),
            ]);
    }
}
