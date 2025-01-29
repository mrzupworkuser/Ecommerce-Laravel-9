<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssetCategory>
 */
class AssetCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'tenant_id' => function () {
                return Tenant::factory()->create();
            },
            'created_by' => function () {
                return User::factory()->create();
            },
            'domain_id' => function () {
                return Domain::factory()->create();
            },
        ];
    }
}
