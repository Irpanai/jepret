<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
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
            'jumlah_tarik' => fake()->numberBetween(100, 500) * 10000, // 1m - 5m
            'metode_pembayaran' => fake()->randomElement(['BCA', 'MANDIRI', 'BRI', 'BNI']),
            'nomor_tujuan' => fake()->numerify('##########'),
            'status' => fake()->randomElement(['pending', 'success', 'success']),
            'review_status' => 'pending',
        ];
    }
}
