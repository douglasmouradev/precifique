<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitcherTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_guest_can_switch_locale_to_english(): void
    {
        $this->get('/');

        $response = $this->post('/locale', ['locale' => 'en']);

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));

        $this->get('/');

        $this->assertSame('en', App::getLocale());
    }

    public function test_tenant_locale_is_persisted_when_switching(): void
    {
        $tenant = $this->readyTenant([
            'email' => 'loja@test.com',
            'password' => 'demo1234',
            'locale' => 'pt_BR',
        ]);

        $this->post('/entrar', [
            'email' => 'loja@test.com',
            'password' => 'demo1234',
        ]);

        $this->post('/locale', ['locale' => 'en'])->assertRedirect();

        $tenant->refresh();
        $this->assertSame('en', $tenant->locale);
        $this->assertSame('en', session('locale'));

        $this->get(route('tenant.dashboard'));

        $this->assertSame('en', App::getLocale());
    }
}
