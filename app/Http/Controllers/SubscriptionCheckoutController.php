<?php

namespace App\Http\Controllers;

use App\DokuPaymentGateway;
use App\Models\Package;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionRefund;
use App\SubscriptionBilling;
use App\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class SubscriptionCheckoutController extends Controller
{
    public function store(Request $request, Package $package, SubscriptionBilling $billing, DokuPaymentGateway $doku): RedirectResponse
    {
        abort_unless($package->is_active && ! $package->is_legacy && ! $package->is_custom, 404);
        if ($package->is_trial) {
            $user = $request->user()->loadMissing(['subscription.package']);
            $wasActivePaid = $user->subscription?->isActive() && $user->subscription?->package_id && ! ($user->subscription?->package?->is_trial ?? false);
            $billing->activateTrial($user, $package);

            $message = $wasActivePaid
                ? 'Bonus Trial berhasil diklaim. Masa aktif bertambah 7 hari dan kapasitas storage bertambah 500 MB.'
                : 'Trial berhasil diaktifkan.';

            return redirect()->route('fotografer.dashboard')->with('success', $message);
        }

        $voucherCode = $request->filled('voucher_code') ? $request->string('voucher_code')->toString() : null;
        $order = $billing->createOrder($request->user(), $package, $voucherCode);
        try {
            $doku->ensureSubscriptionPayment($order);
        } catch (Throwable $exception) {
            report($exception);
            $order->update(['status' => 'failed', 'provider_status' => 'gateway_initialization_failed']);
            app(VoucherService::class)->release($order);

            return back()->withErrors(['payment' => 'DOKU belum dapat membuat QRIS. Silakan coba kembali.']);
        }

        return redirect()->route('subscriptions.payment', $order);
    }

    public function show(Request $request, SubscriptionOrder $order): View|RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if ($order->status === 'paid') {
            return redirect()->route('fotografer.dashboard');
        }

        return view('subscriptions.payment', compact('order'));
    }

    public function status(Request $request, SubscriptionOrder $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if ($order->status !== 'pending') {
            Log::info('Subscription payment status poll reached a final state.', [
                'order_id' => $order->order_id,
                'status' => $order->status,
                'user_id' => $request->user()->id,
            ]);
        }

        return response()->json([
            'status' => $order->status,
            'redirect' => $order->status === 'paid' ? route('fotografer.dashboard') : null,
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function requestRefund(Request $request, SubscriptionOrder $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id && $order->status === 'paid', 404);
        $data = $request->validate(['amount' => ['required', 'integer', 'min:1', 'max:'.$order->gross_amount], 'reason' => ['required', 'string', 'max:1000']]);
        SubscriptionRefund::create(['subscription_order_id' => $order->id, 'requested_by' => $request->user()->id, 'amount' => $data['amount'], 'reason' => $data['reason'], 'status' => 'pending']);

        return back()->with('success', 'Permintaan refund dicatat untuk review Super Admin. Akses tidak dicabut otomatis.');
    }
}
