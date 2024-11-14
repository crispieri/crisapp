<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'image' => $this->faker->imageUrl(), // URL de una imagen aleatoria
            'is_active' => $this->faker->boolean(),
        ];
    }
}
