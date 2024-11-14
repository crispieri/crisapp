<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProfile>
 */
class ClientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_type' => $this->faker->randomElement(ClientType::cases()),
            'rut' => $this->faker->regexify('[0-9]{7,8}-[0-9K]'), // Formato RUT chileno
            'business_name' => $this->faker->company(),
            'commune' => $this->faker->city(),
            'region' => $this->faker->state(),
            'giro' => $this->faker->jobTitle(),
        ];
    }
}
