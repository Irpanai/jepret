<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'package_id' => null,
            'status' => 'active',
            'entitlement_snapshot' => ['code' => 'test', 'name' => 'Test', 'storage_quota_bytes' => 5 * 1073741824, 'duration_days' => 30, 'revision' => 1],
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'version' => 1,
            'is_legacy_transition' => false,
        ];
    }
}
