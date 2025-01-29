<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetCategoryRequest;
use App\Http\Resources\AssetCategoryResource;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssetCategoryController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return AssetCategoryResource::collection(tenant()->assetCategories->load('domain'));
    }

    /**
     * @param AssetCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AssetCategoryRequest $request)
    {
        $assetCategory = AssetCategory::create($request->validated() + [
            'tenant_id' => tenant()->getKey(),
            'created_by' => auth()->id(),
            'domain_id' => tenant()->primary_domain->getKey(),
        ]);

        return response()->json([
            'message' => 'Asset category created successfully',
            'category' => AssetCategoryResource::make($assetCategory),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param AssetCategoryRequest $request
     * @param AssetCategory $assetCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AssetCategoryRequest $request, AssetCategory $assetCategory)
    {
        $assetCategory->update($request->validated());

        return response()->json([
            'message' => 'Asset category updated successfully',
            'category' => AssetCategoryResource::make($assetCategory->fresh()),
        ]);
    }


    /**
     * @param Request $request
     * @param AssetCategory $assetCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, AssetCategory $assetCategory)
    {
        $assetCategory->delete();

        return response()->json([
            'message' => 'Asset category deleted successfully',
        ]);
    }
}
