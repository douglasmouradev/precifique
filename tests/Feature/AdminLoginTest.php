<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_seeded_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
            'is_superadmin' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@precifique.com.br',
            'password' => 'Precifique@2026',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'admin@precifique.com.br')->first());
    }

    public function test_ensure_admin_password_is_not_double_hashed(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => 'Precifique@2026',
        ]);

        $this->assertTrue(Hash::check('Precifique@2026', $user->fresh()->password));
    }
}
