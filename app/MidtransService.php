<?php

/*
 * LEGACY PAYMENT INTEGRATION — DISABLED
 *
 * File ini sengaja dipertahankan sebagai referensi migrasi dari Midtrans ke DOKU.
 * Seluruh kode dinonaktifkan dan tidak lagi diregistrasikan atau dipanggil saat runtime.
 *
 * namespace App;
 *
 * use App\Models\PhotoOrder;
 * use App\Models\SubscriptionOrder;
 * use Illuminate\Support\Str;
 * use Midtrans\Config;
 * use Midtrans\Snap;
 *
 * class MidtransService
 * {
 *     public function createSnapTransaction(SubscriptionOrder $order): array
 *     {
 *         // Previously created a Midtrans Snap transaction for subscriptions.
 *     }
 *
 *     public function createPhotoOrderTransaction(PhotoOrder $order): array
 *     {
 *         // Previously created a Midtrans Snap transaction for photo orders.
 *     }
 *
 *     public function hasValidSignature(array $payload): bool
 *     {
 *         // Previously verified Midtrans SHA-512 notification signatures.
 *     }
 *
 *     private function configure(): void
 *     {
 *         // Previously configured the Midtrans SDK from config/midtrans.php.
 *     }
 * }
 */
