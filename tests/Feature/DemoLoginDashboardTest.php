<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class DemoLoginDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_profile_can_login_and_view_dashboard(): void
    {
        $this->artisan('precifique:ensure-demo', [
            '--password' => 'demo1234',
            '--sample-data' => true,
        ])->assertSuccessful();

        $this->post('/entrar', [
            'email' => 'demo@precifique.com.br',
            'password' => 'demo1234',
        ])->assertRedirect(route('tenant.dashboard'));

        $this->get(route('tenant.dashboard'))
            ->assertOk()
            ->assertSee('Doceria da Ana (Demo)');
    }
}
