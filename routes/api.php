<?php

use App\Http\Controllers\api\v1\AssetController;
use App\Http\Controllers\api\v1\CategoryController;
use App\Http\Controllers\api\v1\DeductibleController;
use App\Http\Controllers\api\v1\DomainController;
use App\Http\Controllers\api\v1\OfferController;
use App\Http\Controllers\api\v1\ProductAvailabilityController;
use App\Http\Controllers\api\v1\ProductController;
use App\Http\Controllers\api\v1\ProductInventoryController;
use App\Http\Controllers\api\v1\ProductLocationController;
use App\Http\Controllers\api\v1\RegisterController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

Route::middleware('auth:sanctum')->group(function () {
        Route::controller(DomainController::class)->prefix('domain')->group(function () {
            Route::get('/', 'index');
            Route::get('/{domain}', 'show');
            Route::put('{domain}/profile', 'update');
            Route::put('{domain}/region', 'updateRegionalDetails');
            Route::put('{domain}/schedule', 'updateScheduleDetails');
            Route::post('/store', 'store');
        });
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('product', ProductController::class);
        Route::prefix('/product/{product}')->group(function () {
            Route::controller(ProductAvailabilityController::class)->group(function () {
                Route::get('/availability', 'show');
                Route::post('/availability', 'store');
                Route::post('/availability/check', 'checkAvailability');
                Route::post('/availability/hold', 'holdAvailability');
                Route::post('/availability/book', 'book');
                Route::get('/availability/{productAvailability}/slots', 'getSlots');
            });

            Route::apiResource('location', ProductLocationController::class);
            Route::apiResource('product-inventory', ProductInventoryController::class);
        });

        Route::apiResource('/api-key', ApiKeyController::class)->middleware('abilities:apikey');
        Route::apiResource('offers', OfferController::class);
        Route::apiResource('/deductible', DeductibleController::class);
    });
});
