<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateProductPriceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListProductPricesRequest;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceCollection;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use App\Queries\ProductPriceIndexQuery;
use Illuminate\Http\JsonResponse;

class ProductPriceController extends Controller
{
    public function __construct(
        protected ProductPriceIndexQuery $productPriceIndexQuery,
        protected CreateProductPriceAction $createProductPriceAction,
    ) {}

    public function index(ListProductPricesRequest $request, Product $product): ProductPriceCollection
    {
        $product->loadMissing('baseCurrency');

        $prices = $this->productPriceIndexQuery
            ->execute($product, $request->validated())
            ->appends($request->query());

        return new ProductPriceCollection($prices, $product);
    }

    public function store(StoreProductPriceRequest $request, Product $product): JsonResponse
    {
        $price = $this->createProductPriceAction->execute($product, $request->validated());
        $price->load('currency');

        return (new ProductPriceResource($price))
            ->response()
            ->setStatusCode(201);
    }
}
