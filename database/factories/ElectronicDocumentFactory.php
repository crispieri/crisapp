<?php

namespace Database\Factories;

use App\Models\Order;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectronicDocument>
 */
class ElectronicDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'type' => $this->faker->randomElement(DocumentType::cases()),
            'folio' => $this->faker->numberBetween(1000, 9999),
            'total' => $this->faker->randomFloat(2, 100, 10000),
            'client_name' => $this->faker->name(),
            'client_rut' => $this->faker->regexify('^[0-9]{7,8}-[0-9K]$'), // Formato RUT
            'status' => 'pending',
            'response_data' => null,
        ];
    }
}
