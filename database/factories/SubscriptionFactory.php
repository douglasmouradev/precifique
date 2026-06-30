<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subscription> */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'plan_id' => Plan::factory()->premium(),
            'status' => 'active',
            'stripe_subscription_id' => null,
            'mercadopago_payment_id' => null,
            'starts_at' => now(),
            'ends_at' => null,
        ];
    }

    public function pastDue(): static
    {
        return $this->state(fn () => [
            'status' => 'past_due',
            'ends_at' => now()->addDays(7),
        ]);
    }

    public function stripe(string $subscriptionId = 'sub_test'): static
    {
        return $this->state(fn () => [
            'stripe_subscription_id' => $subscriptionId,
        ]);
    }
}
