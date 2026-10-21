<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('PROMO????'),
            'name' => fake()->words(3, true),
            'scope' => 'photo',
            'discount_type' => 'fixed',
            'discount_value' => 1000,
            'minimum_order_amount' => 10000,
            'is_active' => true,
        ];
    }
}
