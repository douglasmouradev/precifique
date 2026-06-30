<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Plan> */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $slug = fake()->unique()->slug(2);

        return [
            'slug' => $slug,
            'name' => ucfirst(str_replace('-', ' ', $slug)),
            'price_monthly' => fake()->randomFloat(2, 19, 99),
            'stripe_price_id' => null,
            'max_products' => 5,
            'features' => [],
            'has_ai' => false,
            'is_active' => true,
        ];
    }

    public function premium(): static
    {
        return $this->state(fn () => [
            'slug' => 'premium',
            'name' => 'Premium',
            'max_products' => null,
            'has_ai' => true,
        ]);
    }

    public function basic(): static
    {
        return $this->state(fn () => [
            'slug' => 'basic',
            'name' => 'Basic',
            'max_products' => 5,
            'has_ai' => false,
        ]);
    }
}
