<?php

namespace Database\Factories;

use App\Models\PhotoOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PhotoOrder>
 */
class PhotoOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => 'JPR-PHO-'.Str::upper(Str::random(12)),
            'user_id' => User::factory(),
            'gross_amount' => 20000,
            'currency' => 'IDR',
            'provider' => 'midtrans',
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ];
    }
}
