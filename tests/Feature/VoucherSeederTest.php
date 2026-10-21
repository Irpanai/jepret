<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_inactive_subscription_and_photo_vouchers(): void
    {
        $this->seed(VoucherSeeder::class);

        $subscriptionVoucher = Voucher::where('code', 'JEPRETSUB10')->firstOrFail();
        $this->assertSame('subscription', $subscriptionVoucher->scope);
        $this->assertSame('percentage', $subscriptionVoucher->discount_type);
        $this->assertSame(10, $subscriptionVoucher->discount_value);
        $this->assertSame(20000, $subscriptionVoucher->minimum_order_amount);
        $this->assertSame(100, $subscriptionVoucher->usage_limit);
        $this->assertTrue($subscriptionVoucher->applies_to_all_subscription_packages);
        $this->assertFalse($subscriptionVoucher->is_active);
        $this->assertSame(0, $subscriptionVoucher->subscriptionPackages()->count());

        $photoVoucher = Voucher::where('code', 'JEPRETFOTO10')->firstOrFail();
        $this->assertSame('photo', $photoVoucher->scope);
        $this->assertSame('percentage', $photoVoucher->discount_type);
        $this->assertSame(10, $photoVoucher->discount_value);
        $this->assertSame(20000, $photoVoucher->minimum_order_amount);
        $this->assertSame(100, $photoVoucher->usage_limit);
        $this->assertFalse($photoVoucher->is_active);

        $this->assertSame(2, Voucher::count());
    }

    public function test_repeated_runs_preserve_admin_changes_and_unrelated_vouchers(): void
    {
        $this->seed(VoucherSeeder::class);
        $subscriptionVoucher = Voucher::where('code', 'JEPRETSUB10')->firstOrFail();
        $subscriptionVoucher->update([
            'name' => 'Nama dari Super Admin',
            'discount_value' => 25,
            'is_active' => true,
        ]);
        $otherVoucher = Voucher::factory()->create(['code' => 'OTHER10', 'name' => 'Voucher lain']);

        $this->seed(VoucherSeeder::class);

        $subscriptionVoucher->refresh();
        $this->assertSame('Nama dari Super Admin', $subscriptionVoucher->name);
        $this->assertSame(25, $subscriptionVoucher->discount_value);
        $this->assertTrue($subscriptionVoucher->is_active);
        $this->assertSame('Voucher lain', $otherVoucher->fresh()->name);
        $this->assertSame(3, Voucher::count());
    }
}
