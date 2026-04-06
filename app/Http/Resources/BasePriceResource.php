<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class BasePriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->id,
            'currency_id' => $this->currency_id,
            'price' => (float) $this->price,
            'currency' => new CurrencyResource($this->whenLoaded('baseCurrency')),
        ];
    }
}
