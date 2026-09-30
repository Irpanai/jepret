<?php

/*
 * LEGACY PAYMENT ENDPOINT — DISABLED
 *
 * Controller Midtrans dipertahankan sebagai referensi, tetapi seluruh kode sengaja
 * dikomentari. Route notification Midtrans sudah tidak diregistrasikan.
 *
 * namespace App\Http\Controllers;
 *
 * use App\MidtransService;
 * use App\Models\PhotoOrder;
 * use App\Models\SubscriptionOrder;
 * use App\PhotoOrderBilling;
 * use App\SubscriptionBilling;
 * use Illuminate\Http\JsonResponse;
 * use Illuminate\Http\Request;
 *
 * class MidtransWebhookController extends Controller
 * {
 *     public function __invoke(
 *         Request $request,
 *         MidtransService $midtrans,
 *         SubscriptionBilling $subscriptionBilling,
 *         PhotoOrderBilling $photoBilling,
 *     ): JsonResponse {
 *         // Previously validated and processed Midtrans notifications.
 *     }
 * }
 */
