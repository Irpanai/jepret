<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Voucher::firstOrCreate(
            ['code' => 'JEPRETSUB10'],
            [
                'name' => 'Jepret Subscription 10%',
                'description' => 'Diskon 10% untuk semua paket subscription Jepret.',
                'scope' => 'subscription',
                'applies_to_all_subscription_packages' => true,
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'minimum_order_amount' => 20000,
                'usage_limit' => 100,
                'is_active' => false,
            ],
        );

        Voucher::firstOrCreate(
            ['code' => 'JEPRETFOTO10'],
            [
                'name' => 'Jepret Photo 10%',
                'description' => 'Diskon 10% untuk pembelian foto Jepret.',
                'scope' => 'photo',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'minimum_order_amount' => 20000,
                'usage_limit' => 100,
                'is_active' => false,
            ],
        );
    }
}
