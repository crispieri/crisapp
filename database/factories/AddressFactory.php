<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Crea un usuario relacionado
            'street_address' => $this->faker->streetAddress(),
            'commune' => $this->faker->city(),
            'city' => $this->faker->city(),
            'region' => $this->faker->state(),
            'country' => 'Chile',
        ];
    }
}
