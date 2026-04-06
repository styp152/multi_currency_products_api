<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class UpdateProductAction
{
    public function execute(Product $product, array $payload): Product
    {
        $product->update($payload);

        Log::info('Product updated.', [
            'product_id' => $product->id,
            'changed_fields' => array_keys($payload),
        ]);

        return $product;
    }
}
