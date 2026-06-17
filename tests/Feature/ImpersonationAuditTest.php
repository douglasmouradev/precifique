<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TotpService;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class ImpersonationAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_impersonation_creates_audit_log(): void
    {
        $secret = app(TotpService::class)->generateSecret();
        $admin = User::factory()->create([
            'is_superadmin' => true,
            'password' => 'password',
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);
        $tenant = Tenant::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->withSession(['two_factor_verified_at' => now()->timestamp])
            ->post(route('admin.tenants.impersonate', $tenant), [
                'password' => 'password',
            ])
            ->assertRedirect(route('tenant.dashboard'));

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $tenant->id,
            'action' => 'admin.impersonate.start',
        ]);

        $log = AuditLog::where('action', 'admin.impersonate.start')->first();
        $this->assertSame($admin->id, $log->metadata['admin_user_id'] ?? null);
    }
}
