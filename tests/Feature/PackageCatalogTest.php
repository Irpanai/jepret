<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\Package;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_seeder_is_idempotent_and_preserves_legacy_packages_as_inactive(): void
    {
        $legacy = Package::factory()->create([
            'code' => 'basic',
            'nama_paket' => 'Basic',
            'display_name' => 'Basic',
            'is_active' => true,
            'is_legacy' => false,
        ]);

        $this->seed(PackageSeeder::class);
        $this->seed(PackageSeeder::class);

        $this->assertDatabaseCount('packages', 5);
        $this->assertFalse($legacy->fresh()->is_active);
        $this->assertTrue($legacy->fresh()->is_legacy);
        $this->assertSame(
            ['trial', 'starter', 'creator', 'studio'],
            Package::query()->publiclyAvailable()->ordered()->pluck('code')->all(),
        );
    }

    public function test_default_database_seed_keeps_basic_and_pro_as_inactive_legacy_packages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $legacyPackages = Package::whereIn('code', ['basic', 'pro'])->get();

        $this->assertCount(2, $legacyPackages);
        $this->assertTrue($legacyPackages->every(fn (Package $package): bool => $package->is_legacy && ! $package->is_active));
        $this->assertSame(4, Package::query()->publiclyAvailable()->count());
    }

    public function test_pricing_uses_active_database_packages_and_hides_legacy_or_inactive_packages(): void
    {
        $this->seed(PackageSeeder::class);
        Package::where('code', 'starter')->update(['harga' => 31000]);
        Package::where('code', 'creator')->update(['is_active' => false]);

        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('Rp31.000')
            ->assertSee('Trial')
            ->assertSee('Studio')
            ->assertDontSee('>Creator</h3>', false)
            ->assertSee('wa.me/6285156767900', false);
    }

    public function test_superadmin_can_update_package_and_revision_is_incremented_with_an_audit_log(): void
    {
        $admin = User::factory()->superadmin()->create();
        $package = Package::factory()->create([
            'code' => 'starter',
            'display_name' => 'Starter',
            'nama_paket' => 'Starter',
            'revision' => 2,
        ]);

        $this->actingAs($admin)->put(route('superadmin.packages.update', $package), [
            'code' => 'starter',
            'display_name' => 'Starter Plus',
            'description' => 'Paket baru.',
            'features_text' => "Fitur satu\nFitur dua",
            'harga' => 35000,
            'currency' => 'idr',
            'duration_days' => 30,
            'storage_quota_mb' => 6144,
            'sort_order' => 20,
            'is_active' => '1',
        ])->assertRedirect(route('superadmin.packages.index'));

        $package->refresh();
        $this->assertSame('Starter Plus', $package->display_name);
        $this->assertSame(35000, $package->harga);
        $this->assertSame(6144 * 1048576, $package->storage_quota_bytes);
        $this->assertSame(['Fitur satu', 'Fitur dua'], $package->features);
        $this->assertSame(3, $package->revision);
        $this->assertTrue($package->is_active);
        $this->assertSame(1, AdminAuditLog::where('action', 'package.updated')->count());
    }

    public function test_legacy_package_cannot_be_reactivated_by_superadmin(): void
    {
        $admin = User::factory()->superadmin()->create();
        $package = Package::factory()->create([
            'code' => 'basic',
            'display_name' => 'Basic',
            'nama_paket' => 'Basic',
            'is_active' => false,
            'is_legacy' => true,
        ]);

        $this->actingAs($admin)->put(route('superadmin.packages.update', $package), [
            'code' => 'basic',
            'display_name' => 'Basic',
            'harga' => 0,
            'currency' => 'IDR',
            'duration_days' => 30,
            'storage_quota_mb' => 5000,
            'sort_order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('superadmin.packages.index'));

        $this->assertFalse($package->fresh()->is_active);
        $this->assertTrue($package->fresh()->is_legacy);
    }

    public function test_non_superadmin_cannot_manage_packages(): void
    {
        $buyer = User::factory()->pembeli()->create();

        $this->actingAs($buyer)
            ->get(route('superadmin.packages.index'))
            ->assertForbidden();
    }
}
