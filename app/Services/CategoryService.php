<?php

namespace App\CoreLogic\Services;

use App\Models\Category;
use App\Models\Tenant;
use App\CoreLogic\Repositories\CategoryRepository;
use Illuminate\Support\Facades\Event;

class CategoryService extends Service
{
    public string $repositoryName = CategoryRepository::class;

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $category = $this->repository->create($data);

        Event::dispatch('category.created', $category);

        return $category;
    }

    /**
     * @param Category $category
     * @param array $data
     * @return bool
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update(
            collect($data)->only('name', 'description')->toArray()
        );
    }
}
