<?php

namespace App\Http\Controllers;

use App\DokuPaymentGateway;
use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use App\PhotoOrderBilling;
use App\SubscriptionBilling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DokuWebhookController extends Controller
{
    public function __invoke(Request $request, DokuPaymentGateway $doku, SubscriptionBilling $subscriptionBilling, PhotoOrderBilling $photoBilling): JsonResponse
    {
        $payload = $this->normalize($request);
        $orderId = (string) ($payload['order_id'] ?? '');
        $signatureValid = $doku->hasValidNotificationSignature($request);
        if (! $signatureValid) {
            return $this->response('invalid_signature');
        }
        if ($this->hasMissingMandatoryField($payload)) {
            return response()->json(['responseCode' => '4005602', 'responseMessage' => 'Missing Mandatory Field'], 400);
        }

        $subscriptionOrder = SubscriptionOrder::where('order_id', $orderId)
            ->where('provider_external_id', $payload['external_id'])
            ->where('provider', 'doku')->first();
        if ($subscriptionOrder) {
            return $this->response($subscriptionBilling->processNotification($subscriptionOrder, $payload, $signatureValid));
        }

        $photoOrder = PhotoOrder::where('order_id', $orderId)
            ->where('provider_external_id', $payload['external_id'])
            ->where('provider', 'doku')->first();
        if (! $photoOrder) {
            return response()->json(['responseCode' => '4045601', 'responseMessage' => 'Transaction Not Found'], 404);
        }

        return $this->response($photoBilling->processNotification($photoOrder, $payload, $signatureValid));
    }

    private function response(string $result): JsonResponse
    {
        if ($result === 'invalid_signature') {
            return response()->json(['responseCode' => '4015600', 'responseMessage' => 'Unauthorized signature'], 401);
        }
        if (str_starts_with($result, 'invalid_')) {
            return response()->json(['responseCode' => '4005601', 'responseMessage' => 'Invalid Field Format'], 400);
        }

        return response()->json(['responseCode' => '2005600', 'responseMessage' => 'Successful']);
    }

    /** @return array<string, mixed> */
    private function normalize(Request $request): array
    {
        $payload = $request->json()->all();
        $status = match ((string) ($payload['latestTransactionStatus'] ?? '')) {
            '00' => 'settlement',
            '04' => 'refund',
            '05' => 'cancel',
            '06' => 'failed',
            default => 'pending',
        };

        return [
            'order_id' => $payload['originalPartnerReferenceNo'] ?? null,
            'transaction_id' => $payload['originalReferenceNo'] ?? null,
            'external_id' => $payload['originalExternalId'] ?? null,
            'notification_id' => $request->header('X-EXTERNAL-ID'),
            'transaction_status' => $status,
            'status_code' => $payload['latestTransactionStatus'] ?? null,
            'gross_amount' => data_get($payload, 'amount.value'),
            'currency' => data_get($payload, 'amount.currency'),
            'payment_type' => 'qris',
            'transaction_time' => null,
            'signature_key' => $request->header('X-SIGNATURE'),
        ];
    }

    /** @param array<string, mixed> $payload */
    private function hasMissingMandatoryField(array $payload): bool
    {
        return collect(['order_id', 'transaction_id', 'external_id', 'notification_id', 'status_code', 'gross_amount', 'currency'])
            ->contains(fn (string $field): bool => $payload[$field] === null || $payload[$field] === '');
    }
}
