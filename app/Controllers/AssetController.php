<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\CoreLogic\Services\AssetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService,
    ) {
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return AssetResource::collection(
            $this->assetService->all(
                paginate: true,
                allowedFilters: ['name'],
                allowedSorts: ['name', 'quantity', 'capacity_per_quantity', 'shared_between_products', 'shared_between_bookings'],
                load: ['categories'],
            )
        );
    }

    /**
     * @param Asset $asset
     * @return JsonResponse
     */
    public function show(Asset $asset): JsonResponse
    {
        return response()->json(
            AssetResource::make($asset->load('categories'))
        );
    }

    /**
     * @param StoreAssetRequest $request
     * @return JsonResponse
     */
    public function store(StoreAssetRequest $request): JsonResponse
    {
        return response()->json(
            AssetResource::make(
                $this->assetService->create(
                    $request->validated()
                )->load('categories')
            ),
            Response::HTTP_CREATED
        );
    }

    /**
     * @param UpdateAssetRequest $request
     * @param Asset $asset
     * @return JsonResponse
     */
    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        return response()->json(
            AssetResource::make(
                $this->assetService->update(
                    $asset,
                    $request->validated()
                )
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @param Asset $asset
     * @return JsonResponse
     */
    public function destroy(Asset $asset): JsonResponse
    {
        abort_if(auth()->user()->tokenCan('inventory.asset.delete') === false, Response::HTTP_FORBIDDEN);
        abort_if(tenant()->getKey() !== $asset->tenant_id, Response::HTTP_NOT_FOUND);

        $this->assetService->delete($asset);

        return response()->json([
            'message' => 'Asset deleted successfully',
        ], Response::HTTP_OK);
    }
}
