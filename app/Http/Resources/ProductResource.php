<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $basePrice = (new BasePriceResource($this->resource))->resolve($request);
        $additionalPrices = ProductPriceResource::collection($this->whenLoaded('prices'))->resolve($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'currency_id' => $this->currency_id,
            'tax_cost' => (float) $this->tax_cost,
            'manufacturing_cost' => (float) $this->manufacturing_cost,
            'base_currency' => new CurrencyResource($this->whenLoaded('baseCurrency')),
            'prices' => [
                $basePrice,
                ...$additionalPrices,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
