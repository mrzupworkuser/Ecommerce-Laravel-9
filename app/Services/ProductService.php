<?php

namespace App\CoreLogic\Services;

use App\Jobs\UploadProductImagesJob;
use App\Models\Product;
use App\CoreLogic\Repositories\ProductRepository;
use Illuminate\Support\Facades\Event;

class ProductService extends Service
{
    public string $repositoryName = ProductRepository::class;

    /**
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        if (isset($data['mediaFiles'])) {
            UploadProductImagesJob::dispatch($data['mediaFiles']);
            unset($data['mediaFiles']);
        }

        return tap($this->repository->create($data), function (Product $product) {
            Event::dispatch('product.created', $product);
        });
    }

    /**
     * @param Product $product
     * @return Product
     */
    public function get(Product $product): Product
    {
        return $product->load('locations', 'media', 'type');
    }
}
