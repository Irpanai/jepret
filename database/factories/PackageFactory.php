<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'code' => Str::slug($name),
            'nama_paket' => Str::title($name),
            'display_name' => Str::title($name),
            'description' => fake()->sentence(),
            'features' => [fake()->sentence(3), fake()->sentence(3)],
            'harga' => fake()->numberBetween(10, 100) * 1000,
            'currency' => 'IDR',
            'duration_days' => 30,
            'kuota_storage_mb' => 5000,
            'storage_quota_bytes' => 5000 * 1048576,
            'billing_period' => '30 hari',
            'is_custom' => false,
            'is_active' => true,
            'is_trial' => false,
            'is_legacy' => false,
            'sort_order' => 10,
            'revision' => 1,
            'bisa_custom_watermark' => true,
            'bisa_broadcast_lokasi' => false,
        ];
    }
}
