<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TenantMember> */
class TenantMemberFactory extends Factory
{
    protected $model = TenantMember::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'editor',
            'is_active' => true,
        ];
    }

    public function viewer(): static
    {
        return $this->state(fn () => ['role' => 'viewer']);
    }

    public function owner(): static
    {
        return $this->state(fn () => ['role' => 'owner']);
    }
}
