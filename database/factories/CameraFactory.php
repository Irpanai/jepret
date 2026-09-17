<?php

namespace Database\Factories;

use App\Models\Camera;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Camera>
 */
class CameraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fotografer_id' => User::factory()->fotografer(),
            'name' => fake()->words(2, true),
            'brand_model' => fake()->randomElement(['Sony A7 IV', 'Canon EOS R6', 'Nikon Z6 II']),
            'lens' => fake()->randomElement(['70-200mm f/2.8', '85mm f/1.8', '24-70mm f/2.8']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
