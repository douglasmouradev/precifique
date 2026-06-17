<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\TenantNotification;
use App\Services\TenantNotificationService;
use Tests\Concerns\CreatesReadyTenant;
use Tests\Concerns\RefreshDatabase;
use Tests\TestCase;

class TenantNotificationTest extends TestCase
{
    use CreatesReadyTenant;
    use RefreshDatabase;

    public function test_first_sale_creates_milestone_notification(): void
    {
        $tenant = $this->readyTenant();
        $product = Product::factory()->create(['tenant_id' => $tenant->id, 'stock_quantity' => 0]);

        $this->actingAs($tenant, 'tenant')
            ->post(route('tenant.sales.store'), [
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => 25,
                'payment_method' => 'pix',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tenant_notifications', [
            'tenant_id' => $tenant->id,
            'type' => 'milestone',
        ]);
    }

    public function test_notification_service_counts_unread(): void
    {
        $tenant = $this->readyTenant();
        $service = app(TenantNotificationService::class);

        $service->notify($tenant, 'test', 'Título', 'Corpo');
        TenantNotification::where('tenant_id', $tenant->id)->update(['read_at' => now()]);

        $this->assertSame(0, $service->unreadCount($tenant));
    }

    public function test_tenant_can_mark_all_notifications_as_read(): void
    {
        $tenant = $this->readyTenant();
        $service = app(TenantNotificationService::class);
        $service->notify($tenant, 'test', 'Primeira', 'Corpo 1');
        $service->notify($tenant, 'test', 'Segunda', 'Corpo 2');

        $this->actingAs($tenant, 'tenant')
            ->postJson(route('tenant.notifications.read-all'))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(0, $service->unreadCount($tenant));
    }
}
