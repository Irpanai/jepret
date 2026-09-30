<?php

namespace App\Http\Controllers;

use App\DokuPaymentGateway;
use App\DokuPaymentReconciler;
use App\Models\PhotoOrder;
use App\Models\SubscriptionOrder;
use App\PhotoOrderBilling;
use App\SubscriptionBilling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    public function __invoke(Request $request, DokuPaymentGateway $doku, DokuPaymentReconciler $reconciler, SubscriptionBilling $subscriptionBilling, PhotoOrderBilling $photoBilling): JsonResponse
    {
        $signatureValid = $doku->hasValidNotificationSignature($request);
        if (! $signatureValid) {
            return $this->response('invalid_signature');
        }

        $payload = $this->normalize($request, $reconciler);
        $orderId = (string) ($payload['order_id'] ?? '');
        if ($this->hasMissingMandatoryField($payload)) {
            return response()->json(['responseCode' => '4005602', 'responseMessage' => 'Missing Mandatory Field'], 400);
        }

        $subscriptionOrder = SubscriptionOrder::where('order_id', $orderId)
            ->where('provider_transaction_id', $payload['transaction_id'])
            ->where('provider_external_id', $payload['external_id'])
            ->where('provider', 'doku')->first();
        if ($subscriptionOrder) {
            return $this->response($subscriptionBilling->processNotification($subscriptionOrder, $payload, $signatureValid), (string) $payload['transaction_id']);
        }

        $photoOrder = PhotoOrder::where('order_id', $orderId)
            ->where('provider_transaction_id', $payload['transaction_id'])
            ->where('provider_external_id', $payload['external_id'])
            ->where('provider', 'doku')->first();
        if (! $photoOrder) {
            return response()->json(['responseCode' => '4045601', 'responseMessage' => 'Transaction Not Found'], 404);
        }

        return $this->response($photoBilling->processNotification($photoOrder, $payload, $signatureValid), (string) $payload['transaction_id']);
    }

    private function response(string $result, ?string $approvalCode = null): JsonResponse
    {
        if ($result === 'invalid_signature') {
            return response()->json(['responseCode' => '4015600', 'responseMessage' => 'Unauthorized signature'], 401);
        }
        if (str_starts_with($result, 'invalid_')) {
            return response()->json(['responseCode' => '4005601', 'responseMessage' => 'Invalid Field Format'], 400);
        }

        return response()->json(array_filter([
            'responseCode' => '2005600',
            'responseMessage' => 'Request has been processed successfully',
            'approvalCode' => $approvalCode,
        ]));
    }

    /** @return array<string, mixed> */
    private function normalize(Request $request, DokuPaymentReconciler $reconciler): array
    {
        $payload = $request->json()->all();
        $statusCode = (string) ($payload['latestTransactionStatus'] ?? '');
        if (! in_array($statusCode, ['00', '01', '02', '03', '04', '05', '06', '07'], true)) {
            Log::warning('DOKU sent an unknown QRIS status.', [
                'order_id' => $payload['originalPartnerReferenceNo'] ?? null,
                'status' => $statusCode,
            ]);
        }

        return [
            'order_id' => $payload['originalPartnerReferenceNo'] ?? null,
            'transaction_id' => $payload['originalReferenceNo'] ?? null,
            'external_id' => $payload['originalExternalId'] ?? null,
            'notification_id' => $request->header('X-EXTERNAL-ID'),
            'transaction_status' => $reconciler->status($statusCode),
            'status_code' => $statusCode,
            'gross_amount' => data_get($payload, 'amount.value'),
            'currency' => data_get($payload, 'amount.currency'),
            'payment_type' => 'qris',
            'transaction_time' => $payload['paidTime'] ?? null,
        ];
    }

    /** @param array<string, mixed> $payload */
    private function hasMissingMandatoryField(array $payload): bool
    {
        return collect(['order_id', 'transaction_id', 'external_id', 'notification_id', 'status_code', 'gross_amount', 'currency'])
            ->contains(fn (string $field): bool => $payload[$field] === null || $payload[$field] === '');
    }
}
