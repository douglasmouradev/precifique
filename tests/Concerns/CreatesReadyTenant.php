<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\Tenant;

trait CreatesReadyTenant
{
    protected function readyTenant(array $attributes = []): Tenant
    {
        $tenant = Tenant::factory()->create(array_merge([
            'profile_setup_completed' => true,
            'onboarding_completed' => true,
        ], $attributes));

        foreach (['terms', 'privacy'] as $type) {
            $tenant->lgpdConsents()->create([
                'consent_type' => $type,
                'consented_at' => now(),
                'ip_address' => '127.0.0.1',
                'version' => '1.0',
            ]);
        }

        return $tenant;
    }
}
