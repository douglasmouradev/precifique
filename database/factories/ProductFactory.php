<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->words(3, true),
            'stock_quantity' => 10,
            'min_stock_alert' => 2,
            'profit_margin_percent' => 50,
            'niche_type' => 'alimentos',
            'is_active' => true,
        ];
    }
}
