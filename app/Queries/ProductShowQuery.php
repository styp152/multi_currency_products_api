<?php

namespace App\Queries;

use App\Models\Product;

class ProductShowQuery
{
    public function execute(Product $product): Product
    {
        return $product->load(['baseCurrency', 'prices.currency']);
    }
}
