<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class DeleteProductAction
{
    public function execute(Product $product): void
    {
        Log::info('Product deleted.', [
            'product_id' => $product->id,
            'currency_id' => $product->currency_id,
        ]);

        $product->delete();
    }
}
