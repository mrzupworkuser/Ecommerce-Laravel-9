<?php

namespace App\CoreLogic\Services;

use App\Models\Domain;
use App\Models\Tenant;
use App\CoreLogic\Repositories\DomainRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use App\Events\Domain\DomainCreated;

class DomainService extends Service
{
    public string $repositoryName = DomainRepository::class;

    /**
     * @param Domain $domain
     * @param array $data
     * @return Domain
     */
    public function update(Domain $domain, array $data): Domain
    {
        $this->repository->setModel($domain)->update($data);

        Event::dispatch('domain.updated', $domain);

        return $domain->fresh();
    }

    /**
     * @param array $domain
     * @return bool|Domain
     */
    public function create(array $domain): bool | Domain
    {
        $domainModel =  $this->repository->create($domain);
        DomainCreated::dispatch($domainModel->fresh());
        return $domainModel;
    }
}
