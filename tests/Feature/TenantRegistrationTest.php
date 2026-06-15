<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_register_and_is_sent_to_lgpd(): void
    {
        $response = $this->post('/cadastro', [
            'name' => 'Loja Demo',
            'email' => 'nova@loja.com',
            'password' => 'SenhaForte123!',
            'password_confirmation' => 'SenhaForte123!',
            'niche' => 'alimentos',
        ]);

        $response->assertRedirect(route('lgpd.consent'));
        $this->assertAuthenticated('tenant');

        $tenant = Tenant::where('email', 'nova@loja.com')->first();
        $this->assertNotNull($tenant);
        $this->assertSame('alimentos', $tenant->niche->value ?? (string) $tenant->niche);
        $this->assertFalse($tenant->onboarding_completed);
    }

    public function test_lgpd_consent_redirects_to_onboarding(): void
    {
        $tenant = Tenant::factory()->create([
            'onboarding_completed' => false,
            'profile_setup_completed' => false,
        ]);

        $this->actingAs($tenant, 'tenant')
            ->post('/lgpd/consentimento', [
                'terms' => '1',
                'privacy' => '1',
            ])
            ->assertRedirect(route('onboarding.welcome'));
    }

    public function test_onboarding_complete_sets_flags_and_goes_to_dashboard(): void
    {
        $tenant = Tenant::factory()->create([
            'niche' => 'servico',
            'onboarding_completed' => false,
            'profile_setup_completed' => false,
        ]);

        foreach (['terms', 'privacy'] as $type) {
            $tenant->lgpdConsents()->create([
                'consent_type' => $type,
                'consented_at' => now(),
                'ip_address' => '127.0.0.1',
                'version' => '1.0',
            ]);
        }

        $this->actingAs($tenant, 'tenant')
            ->post('/onboarding/setup', [
                'name' => 'Minha Loja',
                'fixed_cost_name' => 'Aluguel',
                'fixed_cost_amount' => '500',
            ])
            ->assertRedirect(route('tenant.dashboard'));

        $tenant->refresh();
        $this->assertTrue($tenant->onboarding_completed);
        $this->assertTrue($tenant->profile_setup_completed);
    }
}
