<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\CreatesEnrolledSuperAdmin;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use CreatesEnrolledSuperAdmin;
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return $this->enrolledSuperAdmin([
            'email' => 'admin@precifique.com.br',
        ]);
    }

    public function test_admin_logout_route_is_registered(): void
    {
        $this->assertTrue(
            Route::has('logout'),
            'A rota logout do admin (Breeze) deve existir.'
        );
    }

    public function test_tenant_logout_uses_separate_route(): void
    {
        $this->assertTrue(Route::has('tenant.logout'));

        $tenantLogout = collect(Route::getRoutes())
            ->first(fn ($route) => $route->getName() === 'tenant.logout');

        $this->assertSame('sair', $tenantLogout?->uri());
    }

    public function test_superadmin_can_access_all_admin_pages(): void
    {
        $admin = $this->superAdmin();

        $routes = [
            'admin.dashboard',
            'admin.tenants.index',
            'admin.tenants.create',
            'admin.plans.index',
            'admin.logs.index',
            'admin.lgpd',
        ];

        foreach ($routes as $route) {
            $this->actingAsEnrolledSuperAdmin($admin)
                ->get(route($route))
                ->assertOk("Falha ao acessar a rota {$route}");
        }
    }

    public function test_admin_without_2fa_is_redirected_to_enrollment(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('profile.two-factor'));
    }

    public function test_admin_can_logout(): void
    {
        $admin = $this->superAdmin();

        $this->actingAsEnrolledSuperAdmin($admin)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_non_superadmin_cannot_access_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
