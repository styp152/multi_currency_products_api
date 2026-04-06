<?php

namespace App\Actions;

use App\Models\Product;

class CreateProductAction
{
    public function execute(array $payload): Product
    {
        return Product::query()->create($payload);
    }
}
