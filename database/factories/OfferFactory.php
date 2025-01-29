<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\States\Offer\Active;

class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word,
            'code' => $this->faker->name,
            'code_type' => collect(config('app.promocode.type'))->random(),
            'amount' => $this->faker->randomNumber(2),
            'status' => Active::class,
            'tenant_id' => fn() => Tenant::factory()->create()->getKey(),
            'created_by' => fn() => User::factory()->create()->getKey(),
            'domain_id' => 1
        ];
    }
}
