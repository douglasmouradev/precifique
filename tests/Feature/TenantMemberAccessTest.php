<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TenantMember;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class TenantMemberAccessTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_team_member_can_access_dashboard_and_logout(): void
    {
        $tenant = $this->readyTenant(['email' => 'owner@precifique.com.br', 'password' => 'demo1234']);

        $member = TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Colaborador',
            'email' => 'colab@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'editor',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'colab@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.dashboard'));

        $this->assertAuthenticatedAs($member, 'tenant_member');

        $this->get(route('tenant.dashboard'))->assertOk();

        $this->post(route('tenant.logout'))->assertRedirect(route('home'));

        $this->assertGuest('tenant_member');
    }

    public function test_viewer_cannot_delete_product(): void
    {
        $tenant = $this->readyTenant(['email' => 'owner2@precifique.com.br', 'password' => 'demo1234']);

        $product = $tenant->products()->create([
            'name' => 'Produto teste',
            'niche_type' => 'alimentos',
            'is_active' => true,
        ]);

        $member = TenantMember::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Visualizador',
            'email' => 'view@precifique.com.br',
            'password' => 'demo1234',
            'role' => 'viewer',
            'is_active' => true,
        ]);

        $this->post('/entrar', [
            'email' => 'view@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->delete(route('tenant.products.destroy', $product))->assertForbidden();
    }
}
