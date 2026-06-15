<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\TenantApiToken;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class TenantWebhookTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_sale_dispatches_webhook(): void
    {
        Http::fake();

        $tenant = $this->readyTenant();
        $tenant->webhooks()->create([
            'url' => 'https://example.com/hook',
            'secret' => 'test-secret',
            'events' => ['sale.created'],
        ]);

        $product = Product::factory()->create(['tenant_id' => $tenant->id, 'stock_quantity' => 0]);

        $plain = TenantApiToken::issue($tenant, 'wh', ['sales:write']);
        $this->withToken($plain)->postJson('/api/v1/sales', [
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 10,
            'payment_method' => 'pix',
        ])->assertCreated();

        Http::assertSent(fn ($req) => $req->url() === 'https://example.com/hook');
    }
}
