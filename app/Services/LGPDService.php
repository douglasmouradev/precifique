<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LgpdConsent;
use App\Models\Tenant;
use App\Models\TenantApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LGPDService
{
    public function recordConsent(Tenant $tenant, Request $request, string $type, string $version): LgpdConsent
    {
        return LgpdConsent::create([
            'tenant_id' => $tenant->id,
            'consent_type' => $type,
            'consented_at' => now(),
            'ip_address' => $request->ip() ?? '0.0.0.0',
            'user_agent' => $request->userAgent(),
            'version' => $version,
        ]);
    }

    public function hasRequiredConsents(Tenant $tenant): bool
    {
        $required = config('lgpd.required_consents', ['terms', 'privacy']);

        foreach ($required as $type) {
            if (! $tenant->lgpdConsents()->where('consent_type', $type)->exists()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function exportTenantData(Tenant $tenant): array
    {
        $tenant->load([
            'products.variableCosts',
            'products.additionalCosts',
            'products.laborCosts',
            'products.technicalSheets',
            'fixedCosts',
            'tenantVariableCosts',
            'sales.product',
            'monthlyGoals',
            'lgpdConsents',
            'subscription.plan',
        ]);

        return [
            'tenant' => $tenant->only([
                'uuid', 'name', 'email', 'niche', 'plan', 'interface_mode', 'usage_mode',
                'onboarding_completed', 'trial_ends_at', 'created_at',
            ]),
            'products' => $tenant->products,
            'fixed_costs' => $tenant->fixedCosts,
            'variable_costs' => $tenant->tenantVariableCosts,
            'sales' => $tenant->sales,
            'monthly_goals' => $tenant->monthlyGoals,
            'subscription' => $tenant->subscription,
            'consents' => $tenant->lgpdConsents,
            'exported_at' => now()->toIso8601String(),
        ];
    }

    public function anonymizeTenant(Tenant $tenant): void
    {
        DB::transaction(function () use ($tenant): void {
            foreach ($tenant->products as $product) {
                $this->deleteStoredPath($product->photo_path);
                $product->variableCosts()->delete();
                $product->additionalCosts()->delete();
                $product->laborCosts()->delete();
                $product->technicalSheets()->delete();
                $product->delete();
            }

            $tenant->sales()->delete();
            $tenant->fixedCosts()->delete();
            $tenant->tenantVariableCosts()->delete();
            $tenant->monthlyGoals()->delete();
            $tenant->lgpdConsents()->delete();
            $tenant->auditLogs()->delete();
            $tenant->subscription()?->delete();
            TenantApiToken::query()->where('tenant_id', $tenant->id)->delete();

            $this->deleteStoredPath($tenant->logo_path);

            Storage::disk('local')->deleteDirectory('reports/'.$tenant->id);
            Storage::disk('local')->deleteDirectory('exports/tenant-'.$tenant->id);

            $anonId = Str::uuid()->toString();

            $tenant->forceFill([
                'name' => 'Conta Removida',
                'email' => "anon_{$anonId}@removed.local",
                'password' => bcrypt(Str::random(32)),
                'logo_path' => null,
                'is_active' => false,
                'trial_ends_at' => null,
                'niche_metadata' => null,
            ])->save();

            $tenant->delete();
        });
    }

    private function deleteStoredPath(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        Storage::disk($disk)->delete($path);
    }
}
