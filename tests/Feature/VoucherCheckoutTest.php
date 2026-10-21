<?php

namespace Tests\Feature;

use App\DokuPaymentGateway;
use App\Models\Package;
use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use App\SubscriptionBilling;
use App\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Tests\TestCase;

class VoucherCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_photo_checkout_preserves_photographer_earnings_and_funds_discount_from_platform(): void
    {
        $buyer = User::factory()->pembeli()->create();
        $firstPhotographer = User::factory()->fotografer()->create();
        $secondPhotographer = User::factory()->fotografer()->create();
        $first = Photo::factory()->for($firstPhotographer, 'fotografer')->create(['harga' => 20000, 'status' => 'active']);
        $second = Photo::factory()->for($secondPhotographer, 'fotografer')->create(['harga' => 30000, 'status' => 'active']);
        Voucher::factory()->create(['code' => 'SAVE3000', 'discount_value' => 3000]);
        $this->mock(DokuPaymentGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('ensurePhotoOrderPayment')->once()->andReturnUsing(fn ($order) => $order);
        });

        $response = $this->actingAs($buyer)->withSession(['cart' => [$first->id => $first->id, $second->id => $second->id]])
            ->post(route('checkout.process'), ['voucher_code' => 'save3000']);

        $order = $buyer->photoOrders()->first();
        $response->assertRedirect(route('checkout.payment', ['order' => $order->order_id]));
        $this->assertSame(47000, (int) $order->gross_amount);
        $this->assertSame(45000, (int) $order->photographer_amount);
        $this->assertSame(2000, (int) $order->platform_revenue);
        $this->assertSame(47000, (int) Transaction::where('photo_order_id', $order->id)->sum('total_bayar'));
        $this->assertSame(45000, (int) Transaction::where('photo_order_id', $order->id)->sum('photographer_amount'));
        $this->assertSame(2000, (int) Transaction::where('photo_order_id', $order->id)->sum('platform_amount'));
    }

    public function test_preview_rejects_wrong_scope(): void
    {
        $buyer = User::factory()->pembeli()->create();
        Voucher::factory()->create(['code' => 'SUBONLY', 'scope' => 'subscription']);

        $this->actingAs($buyer)->postJson(route('vouchers.preview'), ['voucher_code' => 'SUBONLY', 'scope' => 'photo'])
            ->assertUnprocessable()->assertJsonValidationErrors('voucher_code');
    }

    public function test_subscription_discount_changes_payment_only_and_preserves_package_snapshot(): void
    {
        $user = User::factory()->create(['role' => 'fotografer']);
        $package = Package::factory()->create(['harga' => 50000, 'duration_days' => 30, 'storage_quota_bytes' => 5368709120]);
        Voucher::factory()->create(['code' => 'SUB20', 'scope' => 'subscription', 'discount_type' => 'percentage', 'discount_value' => 20, 'maximum_discount' => 7000]);

        $order = app(SubscriptionBilling::class)->createOrder($user, $package, 'sub20');

        $this->assertSame(43000, (int) $order->gross_amount);
        $this->assertSame(50000, (int) $order->original_subtotal);
        $this->assertSame(30, $order->package_snapshot['duration_days']);
        $this->assertSame(5368709120, $order->package_snapshot['storage_quota_bytes']);
    }

    public function test_existing_subscription_voucher_without_package_restrictions_applies_to_all_packages(): void
    {
        $user = User::factory()->create(['role' => 'fotografer']);
        $firstPackage = Package::factory()->create(['harga' => 50000]);
        $secondPackage = Package::factory()->create(['harga' => 75000]);
        $voucher = Voucher::factory()->create(['code' => 'LEGACYALL', 'scope' => 'subscription']);
        $service = app(VoucherService::class);

        $firstPreview = $service->preview($voucher->code, 'subscription', 50000, $user, package: $firstPackage);
        $secondPreview = $service->preview($voucher->code, 'subscription', 75000, $user, package: $secondPackage);

        $this->assertSame(1000, $firstPreview['discount']);
        $this->assertSame(1000, $secondPreview['discount']);
    }

    public function test_subscription_voucher_for_one_package_is_valid_in_preview_and_final_order(): void
    {
        $user = User::factory()->create(['role' => 'fotografer']);
        $package = Package::factory()->create(['harga' => 50000]);
        $voucher = Voucher::factory()->create(['code' => 'ONEPLAN', 'scope' => 'subscription', 'applies_to_all_subscription_packages' => false]);
        $voucher->subscriptionPackages()->attach($package);

        $this->actingAs($user)->postJson(route('vouchers.preview'), ['voucher_code' => 'ONEPLAN', 'scope' => 'subscription', 'package_id' => $package->id])
            ->assertOk()->assertJsonPath('discount', 1000);
        $order = app(SubscriptionBilling::class)->createOrder($user, $package, 'ONEPLAN');

        $this->assertSame($voucher->id, $order->voucher_id);
        $this->assertSame(49000, (int) $order->gross_amount);
    }

    public function test_subscription_voucher_can_apply_to_multiple_selected_packages(): void
    {
        $user = User::factory()->create(['role' => 'fotografer']);
        $packages = Package::factory()->count(3)->create(['harga' => 50000]);
        $voucher = Voucher::factory()->create(['code' => 'MULTIPLAN', 'scope' => 'subscription', 'applies_to_all_subscription_packages' => false]);
        $voucher->subscriptionPackages()->attach([$packages[0]->id, $packages[1]->id]);
        $service = app(VoucherService::class);

        $first = $service->preview('MULTIPLAN', 'subscription', 50000, $user, package: $packages[0]);
        $second = $service->preview('MULTIPLAN', 'subscription', 50000, $user, package: $packages[1]);

        $this->assertSame(1000, $first['discount']);
        $this->assertSame(1000, $second['discount']);
    }

    public function test_subscription_voucher_rejects_unselected_package_in_preview_and_final_order(): void
    {
        $user = User::factory()->create(['role' => 'fotografer']);
        $allowedPackage = Package::factory()->create(['harga' => 50000]);
        $otherPackage = Package::factory()->create(['harga' => 50000]);
        $voucher = Voucher::factory()->create(['code' => 'RESTRICTED', 'scope' => 'subscription', 'applies_to_all_subscription_packages' => false]);
        $voucher->subscriptionPackages()->attach($allowedPackage);

        $this->actingAs($user)->postJson(route('vouchers.preview'), ['voucher_code' => 'RESTRICTED', 'scope' => 'subscription', 'package_id' => $otherPackage->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('voucher_code')
            ->assertJsonPath('errors.voucher_code.0', 'Voucher ini tidak berlaku untuk paket yang dipilih.');

        try {
            app(SubscriptionBilling::class)->createOrder($user, $otherPackage, 'RESTRICTED');
            $this->fail('Order final seharusnya menolak paket yang tidak dipilih.');
        } catch (ValidationException $exception) {
            $this->assertSame('Voucher ini tidak berlaku untuk paket yang dipilih.', $exception->errors()['voucher_code'][0]);
        }

        $this->assertDatabaseMissing('subscription_orders', ['user_id' => $user->id, 'package_id' => $otherPackage->id]);
    }

    public function test_disabled_or_deleted_selected_package_does_not_break_voucher_validation(): void
    {
        $user = User::factory()->create(['role' => 'fotografer']);
        $package = Package::factory()->create(['harga' => 50000, 'is_active' => false]);
        $voucher = Voucher::factory()->create(['code' => 'ARCHIVED', 'scope' => 'subscription', 'applies_to_all_subscription_packages' => false]);
        $voucher->subscriptionPackages()->attach($package);

        $preview = app(VoucherService::class)->preview('ARCHIVED', 'subscription', 50000, $user, package: $package);
        $package->delete();

        $this->assertSame(1000, $preview['discount']);
        $this->assertSame(0, $voucher->subscriptionPackages()->count());
    }
}
