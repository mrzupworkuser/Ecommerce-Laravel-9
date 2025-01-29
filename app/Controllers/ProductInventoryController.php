<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductInventoryRequest;
use App\Http\Resources\ProductInventoryResource;
use App\Models\Product;
use App\Models\ProductInventory;
use App\CoreLogic\Services\ProductInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductInventoryController extends Controller
{
    public function __construct(
        public ProductInventoryService $productInventoryService
    ) {
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function index(Product $product): JsonResponse
    {
        return response()->json(
            ProductInventoryResource::make(
                $product->inventory->load('assets')
            )
        );
    }

    /**
     * @param StoreProductInventoryRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function store(StoreProductInventoryRequest $request, Product $product): JsonResponse
    {
        return response()->json([
            'message' => 'Product inventory created successfully',
            'inventory' => ProductInventoryResource::make(
                $this->productInventoryService->create(
                    $product,
                    $request->validated()
                )->load('assets')
            )
        ]);
    }

    /**
     * @param Product $product
     * @param ProductInventory $productInventory
     * @return JsonResponse
     */
    public function show(Product $product, ProductInventory $productInventory): JsonResponse
    {
        return response()->json(
            ProductInventoryResource::make(
                $productInventory->load('assets')
            )
        );
    }
}
