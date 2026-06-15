<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class AdminTenantManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['is_superadmin' => true]);
    }

    public function test_tenant_index_filters_by_search_query(): void
    {
        Tenant::factory()->create(['name' => 'Padaria Sol', 'email' => 'sol@test.com']);
        Tenant::factory()->create(['name' => 'Oficina Norte', 'email' => 'norte@test.com']);

        $this->actingAs($this->superAdmin())
            ->get(route('admin.tenants.index', ['q' => 'sol@test.com']))
            ->assertOk()
            ->assertSee('Padaria Sol')
            ->assertDontSee('Oficina Norte');
    }

    public function test_admin_can_extend_trial(): void
    {
        $tenant = Tenant::factory()->create([
            'trial_ends_at' => now()->addDay(),
            'plan' => 'basic',
        ]);

        $this->actingAs($this->superAdmin())
            ->patch(route('admin.tenants.extend-trial', $tenant), ['days' => 7])
            ->assertRedirect();

        $this->assertTrue($tenant->fresh()->trial_ends_at->greaterThan(now()->addDays(6)));
    }

    public function test_admin_can_impersonate_active_tenant(): void
    {
        $tenant = Tenant::factory()->create(['is_active' => true]);

        $this->actingAs($this->superAdmin())
            ->post(route('admin.tenants.impersonate', $tenant))
            ->assertRedirect(route('tenant.dashboard'));

        $this->assertTrue(auth('tenant')->check());
        $this->assertSame($tenant->id, auth('tenant')->id());
    }
}
