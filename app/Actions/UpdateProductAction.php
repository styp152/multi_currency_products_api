<?php

namespace App\Actions;

use App\Models\Product;

class UpdateProductAction
{
    public function execute(Product $product, array $payload): Product
    {
        $product->update($payload);

        return $product;
    }
}
