<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CreateProductAction
{
    public function execute(array $payload): Product
    {
        $product = Product::query()->create($payload);

        Log::info('Product created.', [
            'product_id' => $product->id,
            'currency_id' => $product->currency_id,
        ]);

        return $product;
    }
}
