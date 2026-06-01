<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Tenant> */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'niche' => 'alimentos',
            'plan' => 'basic',
            'interface_mode' => 'alimentos',
            'usage_mode' => 'iniciante',
            'onboarding_completed' => true,
            'profile_setup_completed' => true,
            'is_active' => true,
        ];
    }
}
