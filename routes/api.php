<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware('throttle:api')
    ->group(function (): void {
        Route::apiResource('products', ProductController::class);

        Route::get('products/{product}/prices', [ProductPriceController::class, 'index']);
        Route::post('products/{product}/prices', [ProductPriceController::class, 'store']);
    });
