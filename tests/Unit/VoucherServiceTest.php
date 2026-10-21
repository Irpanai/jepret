<?php

namespace Tests\Unit;

use App\Models\PhotoOrder;
use App\Models\User;
use App\Models\Voucher;
use App\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VoucherServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_fixed_and_capped_percentage_discounts_with_integer_idr(): void
    {
        $user = User::factory()->pembeli()->create();
        $fixed = Voucher::factory()->create(['code' => 'FIXED', 'discount_value' => 3000]);
        $percentage = Voucher::factory()->create(['code' => 'PERCENT', 'discount_type' => 'percentage', 'discount_value' => 20, 'maximum_discount' => 4000]);
        $service = app(VoucherService::class);

        $this->assertSame(3000, $service->preview(' fixed ', 'photo', 50000, $user, 5000)['discount']);
        $this->assertSame(4000, $service->preview('percent', 'photo', 50000, $user, 5000)['discount']);
    }

    public function test_rejects_photo_discount_above_platform_commission_and_zero_payment(): void
    {
        $user = User::factory()->pembeli()->create();
        Voucher::factory()->create(['code' => 'TOO-MUCH', 'scope' => 'photo', 'discount_value' => 5001]);

        $this->expectException(ValidationException::class);
        app(VoucherService::class)->preview('too-much', 'photo', 50000, $user, 5000);
    }

    public function test_reservation_enforces_global_and_per_user_limits_and_can_be_released(): void
    {
        $user = User::factory()->pembeli()->create();
        $voucher = Voucher::factory()->create(['code' => 'LIMITED', 'usage_limit' => 1, 'per_user_limit' => 1]);
        $order = PhotoOrder::factory()->for($user)->create();
        $service = app(VoucherService::class);
        $service->reserve('LIMITED', 'photo', 20000, $user, $order, 2000);
        $service->release($order);
        $second = PhotoOrder::factory()->for($user)->create();

        $result = $service->reserve('LIMITED', 'photo', 20000, $user, $second, 2000);

        $this->assertSame(1000, $result['discount']);
        $this->assertDatabaseHas('voucher_redemptions', ['order_id' => $second->id, 'status' => 'reserved']);
    }

    public function test_final_global_reservation_blocks_another_order(): void
    {
        $firstUser = User::factory()->pembeli()->create();
        $secondUser = User::factory()->pembeli()->create();
        Voucher::factory()->create(['code' => 'FINAL', 'usage_limit' => 1]);
        $service = app(VoucherService::class);
        $service->reserve('FINAL', 'photo', 20000, $firstUser, PhotoOrder::factory()->for($firstUser)->create(), 2000);

        $this->expectException(ValidationException::class);
        $service->reserve('FINAL', 'photo', 20000, $secondUser, PhotoOrder::factory()->for($secondUser)->create(), 2000);
    }
}
