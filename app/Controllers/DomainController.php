<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBusinessHoursRequest;
use App\Http\Requests\UpdateDomainRegionalRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Http\Requests\CreateDomainRequest;
use App\Http\Resources\DomainResource;
use App\Http\Resources\TenantResource;
use App\Models\Domain;
use App\Models\Tenant;
use App\CoreLogic\Services\DomainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DomainController extends Controller
{
    public function __construct(
        public DomainService $domainService,
    ) {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            DomainResource::collection(
                $this->domainService->all(paginate: true)
            )
        );
    }

    /**
     * @param Domain $domain
     * @return DomainResource
     */
    public function show(Domain $domain): DomainResource
    {
        abort_if($domain->tenant_id !== tenant()->id, Response::HTTP_FORBIDDEN);

        return new DomainResource($domain);
    }

    /**
     * @param UpdateDomainRequest $request
     * @param Domain $domain
     * @return JsonResponse
     */
    public function update(UpdateDomainRequest $request, Domain $domain)
    {
        $this->domainService->update($domain, $request->validated());

        return response()->json([
            'message' => 'Domain updated successfully',
            'domain' => new DomainResource($domain)
        ], Response::HTTP_OK);
    }

    /**
     * @param UpdateDomainRegionalRequest $request
     * @param Domain $domain
     * @return JsonResponse
     */
    public function updateRegionalDetails(UpdateDomainRegionalRequest $request, Domain $domain)
    {
        $this->domainService->update($domain, $request->validated());

        return response()->json([
            'message' => 'Domain updated successfully',
            'domain' => new DomainResource($domain),
        ], Response::HTTP_OK);
    }

    /**
     * @param UpdateBusinessHoursRequest $request
     * @param Domain $domain
     * @return JsonResponse
     */
    public function updateScheduleDetails(UpdateBusinessHoursRequest $request, Domain $domain)
    {
        $this->domainService->update($domain, [
            'data' => ($domain->data ?? []) + $request->validated()
        ]);

        return response()->json([
            'message' => 'Domain updated successfully',
            'domain' => new DomainResource($domain->fresh()),
        ], Response::HTTP_OK);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDomainRequest $request)
    {
        $domain = $this->domainService->create($request->validated() + [
            'tenant_id' => tenant()->getKey()
        ]);
        return response()->json([
            'message' => 'Domain created successfully',
            'domain' => DomainResource::make($domain),
        ], Response::HTTP_CREATED);
    }
}
