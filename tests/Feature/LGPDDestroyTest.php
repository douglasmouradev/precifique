<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TenantApiToken;
use Tests\Concerns\RefreshDatabase;
use Tests\Concerns\CreatesReadyTenant;
use Tests\TestCase;

class LGPDDestroyTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_account_deletion_requires_password(): void
    {
        $tenant = $this->readyTenant(['password' => 'SenhaCorreta123!']);

        $this->actingAs($tenant, 'tenant')
            ->from(route('tenant.lgpd.portal'))
            ->delete(route('tenant.lgpd.destroy'), [
                'confirm' => 'EXCLUIR',
                'password' => 'senha-errada',
            ])
            ->assertSessionHasErrors('password');

        $this->assertNull($tenant->fresh()->deleted_at);
    }

    public function test_account_deletion_revokes_api_tokens(): void
    {
        $tenant = $this->readyTenant(['password' => 'SenhaCorreta123!']);
        TenantApiToken::issue($tenant, 'test-token');
        $this->assertSame(1, TenantApiToken::where('tenant_id', $tenant->id)->count());

        $this->actingAs($tenant, 'tenant')
            ->delete(route('tenant.lgpd.destroy'), [
                'confirm' => 'EXCLUIR',
                'password' => 'SenhaCorreta123!',
            ])
            ->assertRedirect(route('home'));

        $this->assertSame(0, TenantApiToken::where('tenant_id', $tenant->id)->count());
    }
}
