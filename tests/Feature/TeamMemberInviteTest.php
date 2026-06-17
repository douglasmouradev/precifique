<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TenantMember;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class TeamMemberInviteTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_owner_can_invite_team_member_with_password_confirmation(): void
    {
        $tenant = $this->readyTenant(['email' => 'owner@precifique.com.br', 'password' => 'demo1234']);

        $this->post('/entrar', [
            'email' => 'owner@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.dashboard'));

        $this->post(route('tenant.account.members.store'), [
            'name' => 'Novo Colaborador',
            'email' => 'novo@precifique.com.br',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
            'role' => 'editor',
        ])->assertRedirect();

        $this->assertDatabaseHas('tenant_members', [
            'tenant_id' => $tenant->id,
            'email' => 'novo@precifique.com.br',
            'role' => 'editor',
        ]);

        $member = TenantMember::query()->where('email', 'novo@precifique.com.br')->firstOrFail();

        $this->post(route('tenant.logout'));

        $this->post('/entrar', [
            'email' => 'novo@precifique.com.br',
            'password' => 'senha-segura',
        ])->assertRedirect(route('tenant.dashboard'));

        $this->assertAuthenticatedAs($member, 'tenant_member');
    }

    public function test_invite_fails_when_password_confirmation_does_not_match(): void
    {
        $this->readyTenant(['email' => 'owner2@precifique.com.br', 'password' => 'demo1234']);

        $this->post('/entrar', [
            'email' => 'owner2@precifique.com.br',
            'password' => 'demo1234',
        ]);

        $this->from(route('tenant.account.index'))
            ->post(route('tenant.account.members.store'), [
                'name' => 'Colaborador',
                'email' => 'fail@precifique.com.br',
                'password' => 'senha-segura',
                'password_confirmation' => 'outra-senha',
                'role' => 'viewer',
            ])
            ->assertRedirect(route('tenant.account.index'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('tenant_members', [
            'email' => 'fail@precifique.com.br',
        ]);
    }
}
