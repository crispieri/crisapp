<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null, // Asigna un ID de categoría padre si es necesario
            'category_name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'image' => $this->faker->imageUrl(), // URL de una imagen aleatoria
            'is_active' => $this->faker->boolean(),
        ];
    }
}
