<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeductibleRequest;
use App\Http\Requests\UpdateDeductibleRequest;
use App\Http\Resources\DeductibleResource;
use App\Models\Deductible;
use App\CoreLogic\Services\DeductibleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class DeductibleController extends Controller
{
    public function __construct(
        public DeductibleService $deductibleService,
    ) {
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return DeductibleResource::collection(
            $this
                ->deductibleService
                ->all(
                    paginate: true,
                    allowedFilters: [
                        'name',
                        'category',
                        'type',
                        'value',
                        'is_price_inclusive',
                        'is_compounded',
                    ],
                    allowedSorts: [
                        'name',
                        'category',
                        'type',
                        'value',
                        'is_price_inclusive',
                        'is_compounded',
                    ],
                    load: ['tenant', 'createdBy']
                )
        );
    }

    /**
     * @param  StoreDeductibleRequest  $request
     * @return JsonResponse
     */
    public function store(StoreDeductibleRequest $request): JsonResponse
    {
        $deductible = $this->deductibleService->create($request->validated());

        return response()->json([
            'message' => 'Deductible created successfully',
            'deductible' => DeductibleResource::make($deductible),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param  Deductible  $deductible
     * @return JsonResponse
     */
    public function show(Deductible $deductible): JsonResponse
    {
        return response()->json(
            DeductibleResource::make($this->deductibleService->get($deductible)),
            Response::HTTP_OK
        );
    }

    /**
     * @param  UpdateDeductibleRequest  $request
     * @param  Deductible  $deductible
     * @return JsonResponse
     */
    public function update(UpdateDeductibleRequest $request, Deductible $deductible): JsonResponse
    {
        $deductible = $this->deductibleService->update($request->validated(), $deductible);
        return response()->json([
            'message' => 'Deductible updated successfully',
            'deductible' => DeductibleResource::make($deductible),
        ], Response::HTTP_OK);
    }

    /**
     * @param  Deductible  $deductible
     * @return JsonResponse
     */
    public function destroy(Deductible $deductible): JsonResponse
    {
        $this->deductibleService->archive($deductible);
        return response()->json([
            'message' => 'Deductible deleted successfully',
        ], Response::HTTP_OK);
    }
}
