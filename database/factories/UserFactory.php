<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'pembeli',
            'saldo' => 0,
            'storage_terpakai_mb' => 0,
            'is_verified' => true,
            'is_active' => true,
        ];
    }

    public function fotografer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'fotografer',
            'saldo' => fake()->numberBetween(100000, 5000000),
            'storage_terpakai_mb' => fake()->numberBetween(100, 10000),
            'is_verified' => fake()->boolean(80), // 80% verified
        ]);
    }

    public function pembeli(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pembeli',
            'saldo' => 0,
            'storage_terpakai_mb' => 0,
            'is_verified' => true,
        ]);
    }

    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
            'saldo' => 0,
            'storage_terpakai_mb' => 0,
            'is_verified' => true,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'is_verified' => false,
        ]);
    }
}
