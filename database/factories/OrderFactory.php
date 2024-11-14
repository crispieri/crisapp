<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\OrderStatusEnum;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Crea un usuario asociado
            'address_id' => Address::factory(), // Crea un usuario asociado
            'status' => OrderStatusEnum::PENDING, // Estado por defecto
            'grand_total' => $this->faker->randomFloat(2, 10, 500), // Total aleatorio entre 10 y 500
            'notes' => $this->faker->sentence(),
        ];
    }
}
