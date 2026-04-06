<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\ProductPrice;

class CreateProductPriceAction
{
    public function execute(Product $product, array $payload): ProductPrice
    {
        return $product->prices()->create($payload);
    }
}
