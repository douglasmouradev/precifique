<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class QuotePdfTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_tenant_can_download_quote_pdf(): void
    {
        $tenant = $this->readyTenant(['email' => 'quote@precifique.com.br', 'password' => 'demo1234']);

        $product = Product::factory()->create([
            'tenant_id' => $tenant->id,
            'selling_price' => 25.00,
            'profit_margin_percent' => 50,
        ]);

        $this->post('/entrar', ['email' => 'quote@precifique.com.br', 'password' => 'demo1234']);

        $this->get(route('tenant.quotes.pdf', $product))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
