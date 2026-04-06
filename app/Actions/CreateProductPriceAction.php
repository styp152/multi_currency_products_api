<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Log;

class CreateProductPriceAction
{
    public function execute(Product $product, array $payload): ProductPrice
    {
        $price = $product->prices()->create($payload);

        Log::info('Additional product price created.', [
            'product_id' => $product->id,
            'product_price_id' => $price->id,
            'currency_id' => $price->currency_id,
        ]);

        return $price;
    }
}
