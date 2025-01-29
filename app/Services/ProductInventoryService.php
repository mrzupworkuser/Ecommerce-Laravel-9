<?php

namespace App\CoreLogic\Services;

use App\Models\Product;
use App\Models\ProductInventory;
use App\CoreLogic\Repositories\ProductInventoryRepository;

class ProductInventoryService extends Service
{
    protected string $repositoryName = ProductInventoryRepository::class;

    /**
     * @param Product $product
     * @param array $data
     * @return ProductInventory
     */
    public function create(Product $product, array $data): ProductInventory
    {
        $inventory = $this->repository->create(array_merge($data, [
            'product_id' => $product->getKey(),
            'quantity' => $data['quantity'] ?? null,
        ]));

        $inventory->assets()->sync($data['assets'] ?? []);

        return $inventory;
    }
}
