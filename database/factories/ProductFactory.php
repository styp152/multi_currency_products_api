<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 2000),
            'currency_id' => Currency::factory(),
            'tax_cost' => fake()->randomFloat(2, 1, 250),
            'manufacturing_cost' => fake()->randomFloat(2, 1, 1000),
        ];
    }
}
