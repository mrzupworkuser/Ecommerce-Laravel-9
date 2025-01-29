<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductLocationRequest;
use App\Http\Resources\ProductLocationResource;
use App\Models\Product;
use App\CoreLogic\Services\ProductLocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductLocationController extends Controller
{
    public function __construct(
        protected ProductLocationService $productLocationService
    ) {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return ProductLocationResource::collection(
            $this->productLocationService->all(
                paginate: true,
                allowedFilters: ['product_id', 'address_type', 'country', 'state', 'city'],
                allowedSorts: ['product_id', 'address_type', 'country', 'state', 'city'],
            )
        );
    }

    /**
     * @param StoreProductLocationRequest $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductLocationRequest $request, Product $product)
    {
        return response()->json(
            new ProductLocationResource(
                $this->productLocationService->update(
                    $product,
                    $request->validated()
                )
            ),
            Response::HTTP_OK
        );
    }
}
