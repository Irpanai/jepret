<?php

namespace App\Http\Controllers;

use App\MidtransService;
use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use App\PhotoOrderBilling;
use App\SubscriptionBilling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, MidtransService $midtrans, SubscriptionBilling $subscriptionBilling, PhotoOrderBilling $photoBilling): JsonResponse
    {
        $payload = $request->json()->all();
        $orderId = (string) ($payload['order_id'] ?? '');
        $signatureValid = $midtrans->hasValidSignature($payload);

        $subscriptionOrder = SubscriptionOrder::where('order_id', $orderId)->first();
        if ($subscriptionOrder) {
            $result = $subscriptionBilling->processNotification($subscriptionOrder, $payload, $signatureValid);

            return response()->json(['result' => $result], str_starts_with($result, 'invalid_') ? 422 : 200);
        }

        $photoOrder = PhotoOrder::where('order_id', $orderId)->first();
        if (! $photoOrder) {
            return response()->json(['message' => 'Unknown order.'], 404);
        }

        $result = $photoBilling->processNotification($photoOrder, $payload, $signatureValid);

        return response()->json(['result' => $result], str_starts_with($result, 'invalid_') ? 422 : 200);
    }
}
