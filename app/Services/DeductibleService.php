<?php

namespace App\CoreLogic\Services;

use App\Events\DeductibleCreated;
use App\Events\DeductibleDeleted;
use App\Events\DeductibleUpdated;
use App\Models\Deductible;
use App\CoreLogic\Repositories\DeductibleRepository;

class DeductibleService extends Service
{
    protected string $repositoryName = DeductibleRepository::class;

    /**
     * @param  array  $deductible
     * @return bool|Deductible
     */
    public function create(array $deductible): bool|Deductible
    {
        $deductibleModel = $this->repository->create($deductible);
        DeductibleCreated::dispatch($deductibleModel->fresh());
        return $deductibleModel;
    }

    /**
     * @param  array  $payload
     * @param $deductible
     * @return Deductible
     */
    public function update(array $payload, $deductible): Deductible
    {
        $deductible->update($payload);
        DeductibleUpdated::dispatch($deductible->fresh());
        return $deductible;
    }

    /**
     * @param  Deductible  $deductible
     * @return void
     */
    public function archive(Deductible $deductible): void
    {
        $deductible->delete();
        DeductibleDeleted::dispatch($deductible->fresh());
    }

    /**
     * @param Deductible $deductible
     * @return Deductible
     */
    public function get(Deductible $deductible): Deductible
    {
        return $deductible;
    }
}
