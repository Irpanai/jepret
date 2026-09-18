<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $harga = fake()->randomElement([15000, 25000, 35000]);
        $tip = fake()->randomElement([0, 0, 5000, 10000]);
        $photographerAmount = Photo::photographerAmount($harga, $tip);

        return [
            'pembeli_id' => User::factory()->pembeli(),
            'photo_id' => Photo::factory(),
            'harga_foto' => $harga,
            'tip_amount' => $tip,
            'total_bayar' => $harga + $tip,
            'status' => fake()->randomElement(['pending', 'paid', 'paid']),
            'payment_status' => 'pending',
            'photographer_amount' => $photographerAmount,
            'platform_amount' => Photo::platformAmount($harga),
            'revenue_share_snapshot' => ['photographer_percent' => 90, 'platform_percent' => 10],
        ];
    }
}
