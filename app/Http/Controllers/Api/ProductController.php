<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateProductAction;
use App\Actions\DeleteProductAction;
use App\Actions\UpdateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListProductsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Queries\ProductIndexQuery;
use App\Queries\ProductShowQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductIndexQuery $productIndexQuery,
        protected ProductShowQuery $productShowQuery,
        protected CreateProductAction $createProductAction,
        protected UpdateProductAction $updateProductAction,
        protected DeleteProductAction $deleteProductAction,
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
        $product = $this->productShowQuery->execute($product);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($this->productShowQuery->execute($product));
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product = $this->updateProductAction->execute($product, $request->validated());

        return new ProductResource($this->productShowQuery->execute($product));
    }

    public function destroy(Product $product): Response
    {
        $this->deleteProductAction->execute($product);

        return response()->noContent();
    }
}
