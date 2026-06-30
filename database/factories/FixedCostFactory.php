<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\FixedCost;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FixedCost> */
class FixedCostFactory extends Factory
{
    protected $model = FixedCost::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->words(2, true),
            'amount' => fake()->randomFloat(2, 50, 500),
            'is_active' => true,
        ];
    }
}
