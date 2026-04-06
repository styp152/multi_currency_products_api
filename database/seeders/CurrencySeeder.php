<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1],
            ['name' => 'EUR', 'symbol' => 'EUR', 'exchange_rate' => 0.92],
            ['name' => 'COP', 'symbol' => '$', 'exchange_rate' => 3900],
            ['name' => 'MXN', 'symbol' => '$', 'exchange_rate' => 16.75],
        ];

        foreach ($currencies as $currency) {
            Currency::query()->updateOrCreate(
                ['name' => $currency['name']],
                $currency,
            );
        }
    }
}
