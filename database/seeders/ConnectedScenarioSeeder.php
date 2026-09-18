<?php

namespace Database\Seeders;

use App\Models\Camera;
use App\Models\Event;
use App\Models\Package;
use App\Models\Photo;
use App\Models\PlatformSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ConnectedScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basic = Package::updateOrCreate(['nama_paket' => 'Basic'], ['code' => 'basic', 'display_name' => 'Basic', 'harga' => 0, 'kuota_storage_mb' => 5000, 'bisa_custom_watermark' => true, 'bisa_broadcast_lokasi' => false]);
        $pro = Package::updateOrCreate(['nama_paket' => 'Pro'], ['code' => 'pro', 'display_name' => 'Pro', 'harga' => 99000, 'kuota_storage_mb' => 50000, 'bisa_custom_watermark' => true, 'bisa_broadcast_lokasi' => true]);
        $admin = User::updateOrCreate(['email' => 'superadmin@jepret.test'], ['name' => 'Super Admin', 'password' => Hash::make('password'), 'role' => 'superadmin', 'is_verified' => true]);
        $creator = User::updateOrCreate(['email' => 'fotografer_pro@jepret.test'], ['name' => 'Creator Pro', 'password' => Hash::make('password'), 'role' => 'fotografer', 'package_id' => $pro->id, 'is_verified' => true, 'verified_at' => now(), 'studio_name' => 'Jepret Studio', 'slug' => 'creator-pro', 'location' => 'Makassar', 'category' => 'Sports', 'saldo' => 180000]);
        $second = User::updateOrCreate(['email' => 'fotografer_basic@jepret.test'], ['name' => 'Creator Basic', 'password' => Hash::make('password'), 'role' => 'fotografer', 'package_id' => $basic->id, 'is_verified' => true, 'slug' => 'creator-basic', 'location' => 'Banjarbaru']);
        User::updateOrCreate(['email' => 'fotografer_pending@jepret.test'], ['name' => 'Creator Pending', 'password' => Hash::make('password'), 'role' => 'fotografer', 'package_id' => $basic->id, 'is_verified' => false]);
        User::updateOrCreate(['email' => 'fotografer_rejected@jepret.test'], ['name' => 'Creator Rejected', 'password' => Hash::make('password'), 'role' => 'fotografer', 'package_id' => $basic->id, 'is_verified' => false, 'verification_rejection_reason' => 'Profil belum lengkap', 'rejected_at' => now(), 'reviewed_by' => $admin->id]);
        $buyer = User::updateOrCreate(['email' => 'pembeli_vip@jepret.test'], ['name' => 'Buyer VIP', 'password' => Hash::make('password'), 'role' => 'pembeli', 'is_verified' => true]);
        Camera::updateOrCreate(['fotografer_id' => $creator->id, 'name' => 'Kamera Utama'], ['brand_model' => 'Sony A7 IV', 'lens' => '70-200mm f/2.8']);
        $event = Event::updateOrCreate(['fotografer_id' => $creator->id, 'nama_event' => 'Makassar Run'], ['tanggal_event' => now()->subDays(14), 'lokasi' => 'Makassar']);
        $photo = Photo::updateOrCreate(['event_id' => $event->id, 'fotografer_id' => $creator->id, 'title' => 'Finish Makassar Run'], ['file_asli' => 'photos/original/demo.jpg', 'file_watermark' => 'photos/preview/demo.jpg', 'purchased_path' => 'photos/purchased/demo.jpg', 'harga' => 20000, 'status' => 'active', 'published_at' => now()->subDays(14), 'views_count' => 40, 'file_size_mb' => 12, 'storage_bytes' => 12582912]);
        foreach (range(1, 30) as $day) {
            Transaction::updateOrCreate(['order_number' => 'DEMO-'.$day, 'photo_id' => $photo->id], ['pembeli_id' => $buyer->id, 'fotografer_id' => $creator->id, 'harga_foto' => 20000, 'tip_amount' => 0, 'total_bayar' => 20000, 'photographer_amount' => 18000, 'platform_amount' => 2000, 'revenue_share_snapshot' => ['photographer_percent' => 90, 'platform_percent' => 10], 'status' => 'paid', 'payment_status' => 'paid', 'payment_method' => 'qris', 'paid_at' => now()->subDays($day), 'created_at' => now()->subDays($day)]);
        }
        foreach (['pending', 'held', 'success', 'rejected'] as $index => $status) {
            Withdrawal::updateOrCreate(['fotografer_id' => $creator->id, 'nomor_tujuan' => '000'.$index], ['jumlah_tarik' => 25000, 'metode_pembayaran' => 'BCA', 'destination_type' => 'bank', 'status' => $status, 'review_status' => $status, 'admin_notes' => $status === 'rejected' ? 'Data tujuan tidak cocok' : null]);
        }
        foreach (PlatformSetting::DEFAULTS as $key => $value) {
            PlatformSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
