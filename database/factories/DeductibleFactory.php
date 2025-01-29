<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\CoreLogic\Enum\Deductible\DeductibleCategoryEnum;
use App\CoreLogic\Enum\Deductible\DeductibleTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deductible>
 */
class DeductibleFactory extends Factory
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
            'name' => $this->faker->word(),
            'category' => $this->faker->randomElement(DeductibleCategoryEnum::cases()),
            'type' => $this->faker->randomElement(DeductibleTypeEnum::cases()),
            'value' => $this->faker->randomFloat('2', 0, 100),
            'is_price_inclusive' => $this->faker->boolean,
            'is_compounded' => $this->faker->boolean,
            'tenant_id' => $tenant->getKey(),
            'domain_id' => $tenant->primary_domain->getKey(),
            'created_by' => $tenant->teams->first()->users->first()->getKey(),
        ];
    }
}
