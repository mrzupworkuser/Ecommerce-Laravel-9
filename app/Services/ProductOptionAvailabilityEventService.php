<?php

namespace App\CoreLogic\Services;

use App\Models\ProductOption;
use App\CoreLogic\Repositories\ProductAvailabilityEventRepository;

class ProductOptionAvailabilityEventService extends Service
{
    protected string $repositoryName = ProductAvailabilityEventRepository::class;

    /**
     * @param ProductOption $productOption
     * @param string $date
     * @return Collection
     */
    public function getAvailableSessions(ProductOption $productOption, string $date): Collection
    {
        return $this
            ->repository
            ->getByProductOptionAndDate($productOption, $date);
    }
}
