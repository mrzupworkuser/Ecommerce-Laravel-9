<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tenant = Tenant::factory()->create();
        return [
            'name' => $this->faker->name,
            'quantity' => $this->faker->numberBetween(0, 100),
            'capacity_per_quantity' => $this->faker->numberBetween(0, 100),
            'shared_between_products' => $this->faker->boolean,
            'shared_between_bookings' => $this->faker->boolean,
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => fn() => $tenant->teams->first()->users->first()->getKey(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Asset $asset) {
            $asset->categories()->attach(Category::factory()->count(3)->create([
                'type' => 'asset',
                'tenant_id' => $asset->tenant_id,
                'domain_id' => $asset->domain_id,
                'created_by' => $asset->created_by,
            ]));
        });
    }
}
