<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListProductsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Queries\ProductIndexQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductIndexQuery $productIndexQuery,
        protected CreateProductAction $createProductAction,
    ) {}

    public function index(ListProductsRequest $request): AnonymousResourceCollection
    {
        $products = $this->productIndexQuery
            ->execute($request->validated())
            ->appends($request->query());

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->createProductAction->execute($request->validated());
        $product->load(['baseCurrency', 'prices.currency']);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        $product->load(['baseCurrency', 'prices.currency']);

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());
        $product->load(['baseCurrency', 'prices.currency']);

        return new ProductResource($product);
    }

    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }
}
