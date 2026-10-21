<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminVoucherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_a_normalized_voucher(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)->post(route('superadmin.vouchers.store'), [
            'code' => ' hemat10 ', 'name' => 'Hemat 10', 'scope' => 'subscription', 'discount_type' => 'percentage',
            'discount_value' => 10, 'maximum_discount' => 10000, 'minimum_order_amount' => 1000, 'usage_limit' => 100,
            'per_user_limit' => 1, 'is_active' => 1, 'applies_to_all_packages' => 1,
        ])->assertRedirect(route('superadmin.vouchers.index'));

        $this->assertDatabaseHas('vouchers', ['code' => 'HEMAT10', 'created_by' => $admin->id, 'is_active' => true]);
    }

    public function test_superadmin_can_limit_subscription_voucher_to_selected_database_packages(): void
    {
        $admin = User::factory()->superadmin()->create();
        $packages = Package::factory()->count(3)->create();

        $this->actingAs($admin)->post(route('superadmin.vouchers.store'), [
            'code' => 'SELECTED', 'name' => 'Selected packages', 'scope' => 'subscription', 'discount_type' => 'fixed',
            'discount_value' => 1000, 'minimum_order_amount' => 0, 'package_ids' => [$packages[0]->id, $packages[2]->id], 'is_active' => 1,
        ])->assertRedirect(route('superadmin.vouchers.index'));

        $voucher = Voucher::where('code', 'SELECTED')->firstOrFail();
        $this->assertFalse($voucher->applies_to_all_subscription_packages);
        $this->assertSame([$packages[0]->id, $packages[2]->id], $voucher->subscriptionPackages()->orderBy('packages.id')->pluck('packages.id')->all());
    }

    public function test_superadmin_form_lists_packages_from_database_and_hides_legacy_packages(): void
    {
        $admin = User::factory()->superadmin()->create();
        $active = Package::factory()->create(['display_name' => 'Dynamic Active Plan']);
        $legacy = Package::factory()->create(['display_name' => 'Old Legacy Plan', 'is_legacy' => true]);

        $response = $this->actingAs($admin)->get(route('superadmin.vouchers.create'));

        $response->assertSee($active->display_name)->assertDontSee($legacy->display_name);
    }

    public function test_subscription_voucher_requires_a_package_when_not_applying_to_all(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)->post(route('superadmin.vouchers.store'), [
            'code' => 'NO-PACKAGE', 'name' => 'No package', 'scope' => 'subscription', 'discount_type' => 'fixed',
            'discount_value' => 1000, 'minimum_order_amount' => 0, 'is_active' => 1,
        ])->assertSessionHasErrors('package_ids');

        $this->assertDatabaseMissing('vouchers', ['code' => 'NO-PACKAGE']);
    }

    public function test_non_superadmin_cannot_manage_vouchers(): void
    {
        $buyer = User::factory()->pembeli()->create();

        $this->actingAs($buyer)->get(route('superadmin.vouchers.index'))->assertForbidden();
    }

    public function test_toggle_changes_active_state_without_deleting_history(): void
    {
        $admin = User::factory()->superadmin()->create();
        $voucher = Voucher::factory()->create(['is_active' => true]);

        $this->actingAs($admin)->delete(route('superadmin.vouchers.toggle', $voucher))->assertRedirect();

        $this->assertFalse($voucher->fresh()->is_active);
        $this->assertModelExists($voucher);
    }
}
