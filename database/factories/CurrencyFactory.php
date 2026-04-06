<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->currencyCode(),
            'symbol' => fake()->randomElement(['$', 'EUR', 'COP', 'MXN']),
            'exchange_rate' => fake()->randomFloat(4, 0.1, 5000),
        ];
    }
}
