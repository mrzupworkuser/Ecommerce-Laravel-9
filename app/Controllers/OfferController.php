<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Filters\Offers\SearchText;
use App\Models\Offer;
use App\Models\Sorts\Offers\ValiditySort;
use App\CoreLogic\Services\OfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class OfferController extends Controller
{
    public function __construct(
        public OfferService $offerService,
    ) {
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return OfferResource::collection(
            $this
                ->offerService
                ->all(
                    paginate: true,
                    allowedFilters: [
                        'title', 'code', 'code_type', 'amount', 'status',
                        AllowedFilter::custom('search_text', new SearchText(), 'title')
                    ],
                    allowedSorts: [
                        'title', 'code', 'code_type', 'amount', 'status',
                        AllowedSort::custom('validity', new ValiditySort(), 'expired_at')
                    ],
                    load: ['tenant', 'createdBy']
                )
        );
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(StoreOfferRequest $request)
    {
        $offer = $this->offerService->create($request->validated());
        return response()->json([
            'message' => 'Offer created successfully',
            'offer' => OfferResource::make($offer),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(Offer $offer)
    {
        return response()->json(OfferResource::make($this->offerService->get($offer)), Response::HTTP_OK);
    }

    /**
     * @param  UpdateOfferRequest  $request
     * @param  Offer  $offer
     * @return JsonResponse
     */
    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $this->offerService->update($request->validated(), $offer);
        return response()->json([
            'message' => 'Successfully updated offer!!',
            'offer' => OfferResource::make($offer->fresh()),
        ], Response::HTTP_OK);
    }

    /**
     * @param  Request  $request
     * @param  Offer  $offer
     * @return JsonResponse
     */
    public function destroy(Request $request, Offer $offer)
    {
        $this->offerService->archive($offer);
        return response()->json([
            'message' => 'Offer deleted successfully',
        ], Response::HTTP_OK);
    }
}
