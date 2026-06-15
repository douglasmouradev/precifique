<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Sale> */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 5);
        $unit = fake()->randomFloat(2, 10, 200);

        return [
            'tenant_id' => Tenant::factory(),
            'product_id' => Product::factory(),
            'quantity' => $qty,
            'unit_price' => $unit,
            'payment_method' => PaymentMethod::Pix->value,
            'sold_at' => now(),
        ];
    }
}
