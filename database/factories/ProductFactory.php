<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => null, // Asigna un ID de categoría si es necesario
            'brand_id' => null, // Asigna un ID de marca si es necesario
            'product_name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'price' => $this->faker->randomFloat(2, 5, 100), // Precio aleatorio entre 5 y 100
            'images' => $this->faker->imageUrl(), // URL de una imagen aleatoria
            'description' => $this->faker->paragraph(),
            'is_active' => $this->faker->boolean(),
            'is_featured' => $this->faker->boolean(),
            'in_stock' => $this->faker->numberBetween(0, 100),
            'on_sale' => $this->faker->boolean(),
        ];
    }
}
