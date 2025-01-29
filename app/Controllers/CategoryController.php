<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\CoreLogic\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    public function __construct(
        public CategoryService $categoryService,
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => CategoryResource::collection(
                $this->categoryService->all(
                    paginate: true,
                    allowedFilters: ['name', 'type'],
                    allowedSorts: ['name', 'type'],
                    load: ['assets', 'products'],
                )
            )
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Request $request, Category $category): JsonResponse
    {
        return response()->json([
            'data' => CategoryResource::make(
                $this->categoryService->find(
                    $category->getKey(),
                    load: ['assets', 'products'],
                    filters: ['type'],
                )
            )
        ]);
    }

    /**
     * @param StoreCategoryRequest $request
     * @return CategoryResource
     */
    public function store(StoreCategoryRequest $request): CategoryResource
    {
        return new CategoryResource(
            $this
                ->categoryService
                ->create($request->validated())
        );
    }

    /**
     * @param StoreCategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(StoreCategoryRequest $request, Category $category)
    {
        $this->categoryService->update(
            $category,
            $request->validated()
        );

        return response()->json([
            'message' => 'Category updated successfully',
        ], 200);
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        abort_if(tenant()->getKey() !== $category->tenant_id, 403, 'You cannot delete an asset category.');

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}
