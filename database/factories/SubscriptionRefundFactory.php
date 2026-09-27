<?php

namespace Database\Factories;

use App\Models\SubscriptionOrder;
use App\Models\SubscriptionRefund;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionRefund>
 */
class SubscriptionRefundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_order_id' => SubscriptionOrder::factory(),
            'amount' => 29000,
            'reason' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
