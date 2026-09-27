<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Package::query()
            ->whereIn('code', ['basic', 'pro'])
            ->orWhereIn('nama_paket', ['Basic', 'Pro'])
            ->update(['is_active' => false, 'is_legacy' => true]);

        foreach ($this->packages() as $package) {
            Package::updateOrCreate(['code' => $package['code']], $package);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function packages(): array
    {
        return [
            [
                'code' => 'trial', 'nama_paket' => 'Trial', 'display_name' => 'Trial', 'harga' => 0, 'currency' => 'IDR',
                'description' => 'Untuk mencoba Jepret selama 7 hari.', 'features' => ['500 MB Cloud Storage', 'Upload dan kelola foto', 'Protected preview', 'Marketplace access', 'Atur harga foto'],
                'duration_days' => 7, 'billing_period' => '7 hari', 'kuota_storage_mb' => 500, 'storage_quota_bytes' => 500 * 1048576,
                'is_custom' => false, 'is_active' => true, 'is_trial' => true, 'is_legacy' => false, 'sort_order' => 10, 'revision' => 1,
                'bisa_custom_watermark' => true, 'bisa_broadcast_lokasi' => false,
            ],
            [
                'code' => 'starter', 'nama_paket' => 'Starter', 'display_name' => 'Starter', 'harga' => 29000, 'currency' => 'IDR',
                'description' => 'Untuk photographer yang mulai aktif menjual.', 'features' => ['5 GB Cloud Storage', 'Semua fitur utama Jepret', 'Protected preview dan watermark', 'Dashboard photographer', 'Transaksi realtime'],
                'duration_days' => 30, 'billing_period' => '30 hari', 'kuota_storage_mb' => 5120, 'storage_quota_bytes' => 5 * 1073741824,
                'is_custom' => false, 'is_active' => true, 'is_trial' => false, 'is_legacy' => false, 'sort_order' => 20, 'revision' => 1,
                'bisa_custom_watermark' => true, 'bisa_broadcast_lokasi' => false,
            ],
            [
                'code' => 'creator', 'nama_paket' => 'Creator', 'display_name' => 'Creator', 'harga' => 59000, 'currency' => 'IDR',
                'description' => 'Untuk photographer dengan aktivitas dan koleksi lebih besar.', 'features' => ['20 GB Cloud Storage', 'Semua fitur Starter', 'Monitoring penjualan', 'Statistik transaksi dan pendapatan', 'Pengelolaan storage'],
                'duration_days' => 30, 'billing_period' => '30 hari', 'kuota_storage_mb' => 20480, 'storage_quota_bytes' => 20 * 1073741824,
                'is_custom' => false, 'is_active' => true, 'is_trial' => false, 'is_legacy' => false, 'sort_order' => 30, 'revision' => 1,
                'bisa_custom_watermark' => true, 'bisa_broadcast_lokasi' => true,
            ],
            [
                'code' => 'studio', 'nama_paket' => 'Studio', 'display_name' => 'Studio', 'harga' => 0, 'currency' => 'IDR',
                'description' => 'Untuk studio, tim, dan kebutuhan skala besar.', 'features' => ['Kapasitas sesuai kebutuhan', 'Semua fitur Creator', 'Kebutuhan operasional custom', 'Dukungan kebutuhan tim', 'Konfigurasi fleksibel'],
                'duration_days' => null, 'billing_period' => null, 'kuota_storage_mb' => 0, 'storage_quota_bytes' => null,
                'is_custom' => true, 'is_active' => true, 'is_trial' => false, 'is_legacy' => false, 'sort_order' => 40, 'revision' => 1,
                'bisa_custom_watermark' => true, 'bisa_broadcast_lokasi' => true,
            ],
        ];
    }
}
