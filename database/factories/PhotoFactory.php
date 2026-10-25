<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'fotografer_id' => User::factory()->fotografer(),
            'file_asli' => 'photos/raw_'.Str::random(10).'.jpg',
            'file_watermark' => 'photos/wm_'.Str::random(10).'.jpg',
            'harga' => fake()->randomElement([15000, 25000, 35000]),
            'ai_tags' => json_encode(['lari', 'sepeda', 'pagi', 'olahraga']),
            'kamera_body' => fake()->randomElement(['Sony A7III', 'Canon EOS R6', 'Nikon Z6 II']),
            'lensa' => fake()->randomElement(['70-200mm f/2.8', '85mm f/1.8', '24-70mm f/2.8']),
            'eksposur' => '1/1000s, f/2.8, ISO 400',
            'focal_length' => fake()->randomElement(['70mm', '85mm', '200mm']),
            'resolusi' => '6000x4000',
            'sell_without_watermark' => true,
        ];
    }
}
