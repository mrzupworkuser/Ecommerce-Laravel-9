<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone' => $this->faker->phoneNumber,
            'phone_code' => $this->faker->phoneNumber,
            'tenant_id' => fn() => Tenant::factory()->create()->getKey(),
            'created_by' => fn() => User::factory()->create()->getKey(),
            'domain_id' => 1
        ];
    }
}
