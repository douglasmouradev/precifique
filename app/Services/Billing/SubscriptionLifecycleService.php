<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Enums\PlanType;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\AdminMetricsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SubscriptionLifecycleService
{
    public function clearPendingPixCache(int $tenantId): void
    {
        Cache::forget("pix_checkout:{$tenantId}");
    }

    public function expireSubscriptions(): int
    {
        $expired = Subscription::query()
            ->whereIn('status', ['active', 'past_due'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expired as $subscription) {
            $this->deactivatePremium($subscription);
        }

        return $expired->count();
    }

    public function activatePremium(
        int $tenantId,
        int $planId,
        ?string $stripeSubscriptionId,
        ?string $mercadopagoPaymentId,
        ?\DateTimeInterface $endsAt,
    ): bool {
        if (! $tenantId || ! $planId) {
            return false;
        }

        if ($mercadopagoPaymentId && Subscription::where('mercadopago_payment_id', $mercadopagoPaymentId)->exists()) {
            return true;
        }

        if ($stripeSubscriptionId && Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->where('status', 'active')->exists()) {
            $existing = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
            if ($existing && $existing->tenant_id === $tenantId) {
                return true;
            }
        }

        $tenant = Tenant::find($tenantId);
        $plan = Plan::find($planId);

        if (! $tenant || ! $plan) {
            return false;
        }

        $subscriptionEnds = $endsAt;
        if ($subscriptionEnds === null && $mercadopagoPaymentId) {
            $subscriptionEnds = now()->addDays((int) config('tenancy.pix_subscription_days', 30));
        }

        return DB::transaction(function () use ($tenant, $plan, $stripeSubscriptionId, $mercadopagoPaymentId, $subscriptionEnds) {
            $tenant->forceFill(['plan' => PlanType::Premium])->save();

            Subscription::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'stripe_subscription_id' => $stripeSubscriptionId,
                    'mercadopago_payment_id' => $mercadopagoPaymentId,
                    'starts_at' => now(),
                    'ends_at' => $subscriptionEnds,
                ]
            );

            AdminMetricsService::forgetCache();
            $this->clearPendingPixCache($tenant->id);

            return true;
        });
    }

    public function deactivatePremium(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => $subscription->ends_at ?? now(),
            ]);

            $tenant = $subscription->tenant;
            if (! $tenant) {
                return true;
            }

            if ($tenant->onTrial()) {
                return true;
            }

            $tenant->forceFill(['plan' => PlanType::Basic])->save();
            AdminMetricsService::forgetCache();

            return true;
        });
    }
}
