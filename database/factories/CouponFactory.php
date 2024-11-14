<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => strtoupper(Str::random(8)), // Genera un código aleatorio
            'type' => $this->faker->randomElement(['fixed', 'percentage']), // Tipo de descuento
            'discount_value' => $this->faker->randomFloat(2, 5, 50), // Valor de descuento entre 5 y 50 (puede ser un porcentaje o valor fijo)
            'expires_at' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 month'), // Fecha de expiración opcional
        ];
    }

    /**
     * Estado para crear cupones de tipo porcentaje.
     */
    public function percentage()
    {
        return $this->state([
            'type' => 'percentage',
            'discount_value' => $this->faker->numberBetween(5, 30), // Descuento porcentual entre 5% y 30%
        ]);
    }

    /**
     * Estado para crear cupones de tipo fijo.
     */
    public function fixed()
    {
        return $this->state([
            'type' => 'fixed',
            'discount_value' => $this->faker->numberBetween(500, 5000), // Descuento fijo entre 500 y 5000
        ]);
    }
}
