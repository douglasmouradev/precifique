<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LgpdConsent;
use App\Models\Tenant;
use App\Services\LGPDService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LGPDServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_required_consents(): void
    {
        $tenant = Tenant::factory()->create();
        $service = new LGPDService;

        $this->assertFalse($service->hasRequiredConsents($tenant));

        LgpdConsent::create([
            'tenant_id' => $tenant->id,
            'consent_type' => 'terms',
            'consented_at' => now(),
            'ip_address' => '127.0.0.1',
            'version' => '1.0',
        ]);

        $this->assertFalse($service->hasRequiredConsents($tenant));

        LgpdConsent::create([
            'tenant_id' => $tenant->id,
            'consent_type' => 'privacy',
            'consented_at' => now(),
            'ip_address' => '127.0.0.1',
            'version' => '1.0',
        ]);

        $this->assertTrue($service->hasRequiredConsents($tenant));
    }
}
