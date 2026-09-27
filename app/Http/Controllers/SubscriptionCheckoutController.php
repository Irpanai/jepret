<?php

namespace App\Http\Controllers;

use App\MidtransService;
use App\Models\Package;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionRefund;
use App\SubscriptionBilling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionCheckoutController extends Controller
{
    public function store(Request $request, Package $package, SubscriptionBilling $billing, MidtransService $midtrans): RedirectResponse
    {
        abort_unless($package->is_active && ! $package->is_legacy && ! $package->is_custom, 404);
        if ($package->is_trial) {
            $billing->activateTrial($request->user(), $package);

            return redirect()->route('fotografer.dashboard')->with('success', 'Trial berhasil diaktifkan.');
        }

        $order = $billing->createOrder($request->user(), $package);
        if (! $order->snap_token) {
            $snap = $midtrans->createSnapTransaction($order->load('user'));
            $order->update(['snap_token' => $snap['token'], 'snap_redirect_url' => $snap['redirect_url']]);
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
        $order->refresh();

        return response()->json(['status' => $order->status, 'redirect' => $order->status === 'paid' ? route('fotografer.dashboard') : null]);
    }

    public function requestRefund(Request $request, SubscriptionOrder $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id && $order->status === 'paid', 404);
        $data = $request->validate(['amount' => ['required', 'integer', 'min:1', 'max:'.$order->gross_amount], 'reason' => ['required', 'string', 'max:1000']]);
        SubscriptionRefund::create(['subscription_order_id' => $order->id, 'requested_by' => $request->user()->id, 'amount' => $data['amount'], 'reason' => $data['reason'], 'status' => 'pending']);

        return back()->with('success', 'Permintaan refund dicatat untuk review Super Admin. Akses tidak dicabut otomatis.');
    }
}
