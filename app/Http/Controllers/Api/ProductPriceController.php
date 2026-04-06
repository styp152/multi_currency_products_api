<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductPriceController extends Controller
{
    public function index(Product $product): AnonymousResourceCollection
    {
        $prices = $product->prices()
            ->with('currency')
            ->latest('id')
            ->get();

        return ProductPriceResource::collection($prices);
    }

    public function store(StoreProductPriceRequest $request, Product $product): JsonResponse
    {
        $price = $product->prices()->create($request->validated());
        $price->load('currency');

        return (new ProductPriceResource($price))
            ->response()
            ->setStatusCode(201);
    }
}
