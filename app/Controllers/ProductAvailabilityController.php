<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductAvailabilityRequest;
use App\Http\Resources\ProductAvailabilityResource;
use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\ProductOption;
use App\CoreLogic\Services\ProductAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ProductAvailabilityController extends Controller
{
    public function __construct(
        public ProductAvailabilityService $service,
    ) {
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function show(Request $request, Product $product)
    {
        return response()->json(
            ProductAvailabilityResource::collection(
                $product->availabilities,
            )
        );
    }

    /**
     * @param StoreProductAvailabilityRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function store(StoreProductAvailabilityRequest $request, Product $product)
    {
        return response()->json(
            $this->service->update(
                $product,
                $request->validated()
            ),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param ProductAvailability $availability
     * @return JsonResponse
     */
    public function getSlots(ProductAvailability $availability)
    {
        return response()->json(
            $availability->slots,
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return mixed
     */
    public function checkAvailability(Request $request, Product $product)
    {
        $date = Carbon::parse($request->date) ?? Carbon::now();

        return $this->service->checkAvailability(
            $product,
            $date
        );
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function holdAvailability(Request $request, Product $product)
    {
        $slot = Carbon::parse($request->slot);
        $quantity = $request->quantity ?? 1;

        $this->service->holdSlot(
            $product,
            $slot,
            $quantity
        );

        return response()->json(
            [
                'slot' => $slot,
                'quantity' => $quantity,
                'success' => true,
                'message' => 'Slot has been held for next 5 minutes'
            ],
            Response::HTTP_OK
        );
    }
}
