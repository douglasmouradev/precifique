<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Tenant> */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'niche' => 'alimentos',
            'interface_mode' => 'alimentos',
            'usage_mode' => 'iniciante',
            'onboarding_completed' => true,
            'profile_setup_completed' => true,
            'email_verified_at' => now(),
            'locale' => 'pt_BR',
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create($attributes = [], ?\Illuminate\Database\Eloquent\Model $parent = null)
    {
        $plan = $attributes['plan'] ?? null;
        $isActive = $attributes['is_active'] ?? null;
        unset($attributes['plan'], $attributes['is_active']);

        /** @var Tenant $tenant */
        $tenant = parent::create($attributes, $parent);

        $tenant->forceFill([
            'plan' => $plan ?? 'basic',
            'is_active' => $isActive ?? true,
        ])->save();

        return $tenant->fresh();
    }
}
