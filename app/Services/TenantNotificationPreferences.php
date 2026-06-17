<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;

class TenantNotificationPreferences
{
    /** @return array<string, bool> */
    public function defaults(): array
    {
        return [
            'email_low_stock' => true,
            'email_trial' => true,
            'email_payment_failed' => true,
            'email_pix' => true,
            'email_goal' => true,
            'email_monthly_report' => true,
            'in_app' => true,
        ];
    }

    /** @return array<string, bool> */
    public function for(Tenant $tenant): array
    {
        return array_merge($this->defaults(), $tenant->notification_preferences ?? []);
    }

    public function allowsEmail(Tenant $tenant, string $key): bool
    {
        $prefs = $this->for($tenant);

        return (bool) ($prefs[$key] ?? true);
    }

    public function allowsInApp(Tenant $tenant): bool
    {
        return (bool) $this->for($tenant)['in_app'];
    }
}
