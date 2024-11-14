<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(), // Crea una orden asociada
            'product_id' => Product::factory(), // Crea un producto asociado
            'quantity' => $this->faker->numberBetween(1, 10), // Cantidad aleatoria entre 1 y 10
            'unit_amount' => $this->faker->randomFloat(2, 5, 100), // Monto unitario aleatorio entre 5 y 100
            'sub_total' => $this->faker->randomFloat(2, 5, 100), // Subtotal aleatorio entre 5 y 100
        ];
    }
}
