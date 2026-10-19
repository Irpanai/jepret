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
    public function __invoke(
        Request $request,
        DokuPaymentGateway $doku,
        DokuPaymentReconciler $reconciler,
        SubscriptionBilling $subscriptionBilling,
        PhotoOrderBilling $photoBilling
    ): JsonResponse {
        $signatureValid = $doku->hasValidNotificationSignature($request);

        if (! $signatureValid) {
            Log::warning('DOKU QRIS webhook rejected.', [
                'format' => $this->format($request),
                'notification_id' => $request->header('X-EXTERNAL-ID')
                    ?? $request->header('Request-Id'),
                'reason' => 'invalid_signature',
            ]);

            return $this->response('invalid_signature');
        }

        $payload = $this->normalize($request, $reconciler);
        $orderId = (string) ($payload['order_id'] ?? '');

        if ($this->hasMissingMandatoryField($payload)) {
            Log::warning(
                'DOKU QRIS webhook rejected.',
                $this->logContext($payload, 'missing_mandatory_field')
            );

            return response()->json([
                'responseCode' => '4005602',
                'responseMessage' => 'Missing Mandatory Field',
            ], 400);
        }

        $subscriptionOrder = SubscriptionOrder::where('order_id', $orderId)
            ->where('provider', 'doku')
            ->when(
                $payload['format'] === 'snap',
                fn ($query) => $query
                    ->where('provider_transaction_id', $payload['transaction_id'])
                    ->where('provider_external_id', $payload['external_id'])
            )
            ->first();

        if ($subscriptionOrder) {
            $payload = $this->withStoredReferences(
                $payload,
                $subscriptionOrder
            );

            $result = $subscriptionBilling->processNotification(
                $subscriptionOrder,
                $payload,
                $signatureValid
            );

            Log::info(
                'DOKU QRIS webhook processed.',
                $this->logContext($payload, $result, 'subscription')
            );

            return $this->response(
                $result,
                (string) $payload['transaction_id']
            );
        }

        $photoOrder = PhotoOrder::where('order_id', $orderId)
            ->where('provider', 'doku')
            ->when(
                $payload['format'] === 'snap',
                fn ($query) => $query
                    ->where('provider_transaction_id', $payload['transaction_id'])
                    ->where('provider_external_id', $payload['external_id'])
            )
            ->first();

        if (! $photoOrder) {
            Log::warning(
                'DOKU QRIS webhook rejected.',
                $this->logContext($payload, 'transaction_not_found')
            );

            return response()->json([
                'responseCode' => '4045601',
                'responseMessage' => 'Transaction Not Found',
            ], 404);
        }

        $payload = $this->withStoredReferences($payload, $photoOrder);

        $result = $photoBilling->processNotification(
            $photoOrder,
            $payload,
            $signatureValid
        );

        Log::info(
            'DOKU QRIS webhook processed.',
            $this->logContext($payload, $result, 'photo')
        );

        return $this->response(
            $result,
            (string) $payload['transaction_id']
        );
    }

    private function response(
        string $result,
        ?string $approvalCode = null
    ): JsonResponse {
        if ($result === 'invalid_signature') {
            return response()->json([
                'responseCode' => '4015600',
                'responseMessage' => 'Unauthorized signature',
            ], 401);
        }

        if (str_starts_with($result, 'invalid_')) {
            return response()->json([
                'responseCode' => '4005601',
                'responseMessage' => 'Invalid Field Format',
            ], 400);
        }

        return response()->json(array_filter([
            'responseCode' => '2005600',
            'responseMessage' => 'Request has been processed successfully',
            'approvalCode' => $approvalCode,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    private function normalize(
        Request $request,
        DokuPaymentReconciler $reconciler
    ): array {
        $payload = $request->json()->all();

        if ($this->format($request) === 'qris') {
            $status = strtoupper(
                (string) data_get($payload, 'transaction.status')
            );

            return [
                'format' => 'qris',
                'order_id' => data_get(
                    $payload,
                    'order.invoice_number'
                ),
                'transaction_id' => null,
                'external_id' => null,
                'notification_id' => $request->header('Request-Id'),
                'transaction_status' => match ($status) {
                    'SUCCESS' => 'settlement',
                    'FAILED' => 'failed',
                    default => 'pending',
                },
                'status_code' => $status,
                'gross_amount' => data_get($payload, 'order.amount'),
                'currency' => 'IDR',
                'payment_type' => 'qris',
                'transaction_time' => data_get(
                    $payload,
                    'transaction.date'
                ),
                'service_id' => data_get(
                    $payload,
                    'service.id'
                ),
                'api_format' => data_get(
                    $payload,
                    'additional_info.origin.apiFormat'
                ),
            ];
        }

        $statusCode = (string) (
            $payload['latestTransactionStatus'] ?? ''
        );

        if (! in_array(
            $statusCode,
            ['00', '01', '02', '03', '04', '05', '06', '07'],
            true
        )) {
            Log::warning('DOKU sent an unknown QRIS status.', [
                'order_id' => $payload['originalPartnerReferenceNo'] ?? null,
                'status' => $statusCode,
            ]);
        }

        return [
            'format' => 'snap',
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

    /**
     * @param array<string, mixed> $payload
     */
    private function hasMissingMandatoryField(array $payload): bool
    {
        $fields = $payload['format'] === 'qris'
            ? [
                'order_id',
                'notification_id',
                'status_code',
                'gross_amount',
                'currency',
                'transaction_time',
                'service_id',
                'api_format',
            ]
            : [
                'order_id',
                'transaction_id',
                'external_id',
                'notification_id',
                'status_code',
                'gross_amount',
                'currency',
            ];

        $missing = collect($fields)
            ->filter(
                fn (string $field): bool =>
                    ($payload[$field] ?? null) === null
                    || $payload[$field] === ''
            )
            ->values()
            ->all();

        $invalid = [];

        if ($payload['format'] === 'qris') {
            if (($payload['service_id'] ?? null) !== 'QRIS') {
                $invalid[] = 'service_id';
            }

            if (($payload['api_format'] ?? null) !== 'SNAP') {
                $invalid[] = 'api_format';
            }
        }

        if ($missing !== [] || $invalid !== []) {
            Log::warning(
                'DOKU QRIS webhook validation details.',
                [
                    'order_id' => $payload['order_id'] ?? null,
                    'format' => $payload['format'],
                    'missing_fields' => $missing,
                    'invalid_fields' => $invalid,
                ]
            );

            return true;
        }

        return false;
    }

    private function format(Request $request): string
    {
        return (string) $request->header('Signature') !== ''
            ? 'qris'
            : 'snap';
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function withStoredReferences(
        array $payload,
        PhotoOrder|SubscriptionOrder $order
    ): array {
        if ($payload['format'] === 'qris') {
            $payload['transaction_id'] =
                $order->provider_transaction_id;

            $payload['external_id'] =
                $order->provider_external_id;
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, string|null>
     */
    private function logContext(
        array $payload,
        string $result,
        ?string $orderType = null
    ): array {
        return [
            'format' => $payload['format'] ?? null,
            'notification_id' => $payload['notification_id'] ?? null,
            'order_id' => $payload['order_id'] ?? null,
            'order_type' => $orderType,
            'provider_status' => $payload['status_code'] ?? null,
            'result' => $result,
        ];
    }
}
