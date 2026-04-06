<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductPriceCollection extends ResourceCollection
{
    public function __construct($resource, protected Product $product)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'data' => ProductPriceResource::collection($this->collection),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'base_price' => (new BasePriceResource($this->product))->resolve($request),
        ];
    }
}
