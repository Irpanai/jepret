<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\SubscriptionOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionOrder>
 */
class SubscriptionOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => 'JPR-TEST-'.fake()->unique()->uuid(),
            'user_id' => User::factory(),
            'package_id' => Package::factory(),
            'intent' => 'activation',
            'package_snapshot' => ['code' => 'starter', 'name' => 'Starter', 'revision' => 1, 'price' => 29000, 'currency' => 'IDR', 'duration_days' => 30, 'storage_quota_bytes' => 5 * 1073741824],
            'gross_amount' => 29000,
            'currency' => 'IDR',
            'provider' => 'doku',
            'status' => 'pending',
            'source_subscription_version' => 0,
            'expires_at' => now()->addDay(),
        ];
    }
}
