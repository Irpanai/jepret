<?php

namespace App\Http\Controllers;

use App\DokuPaymentGateway;
use App\Models\Photo;
use App\Models\PhotoOrder;
use App\Models\Transaction;
use App\PhotoOrderBilling;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

class PembeliController extends Controller
{
    public function purchases(Request $request): View
    {
        $status = $request->string('status')->lower()->toString();
        $allowedStatuses = ['paid', 'pending', 'failed', 'expired'];

        $transactions = Transaction::where('pembeli_id', $request->user()->id)
            ->with(['photo.fotografer', 'photo.event'])
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status): void {
                $query->where('payment_status', $status);
            })
            ->orderByDesc('created_at')
            ->get();

        $orders = $transactions
            ->groupBy(fn (Transaction $transaction): string => $transaction->order_number ?: 'LEGACY-'.$transaction->id)
            ->map(fn (Collection $items, string $orderNumber): array => $this->orderSummary($orderNumber, $items))
            ->values();

        return view('purchases.index', compact('orders', 'status'));
    }

    public function checkoutPage(): View
    {
        $ids = array_values(session()->get('cart', []));
        $cart = Photo::with(['fotografer', 'event'])
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true)->withActiveSubscription())
            ->get()
            ->map(fn (Photo $photo): array => [
                'id' => $photo->id,
                'price' => (int) $photo->harga,
                'event' => $photo->event->nama_event ?? 'Event',
                'fotografer' => $photo->fotografer->name ?? 'Photographer',
                'preview' => $photo->file_watermark,
            ])
            ->all();
        $total = collect($cart)->sum('price');

        return view('checkout', compact('cart', 'total'));
    }

    public function processCheckout(Request $request, PhotoOrderBilling $billing, DokuPaymentGateway $doku): RedirectResponse
    {
        $user = $request->user();
        $ids = array_values(array_unique(array_map('intval', session()->get('cart', []))));

        if ($ids === []) {
            return redirect()->route('galeri')->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        if (! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('profile.edit')->withErrors(['email' => 'Gunakan alamat email yang valid sebelum melakukan pembayaran.']);
        }

        $photos = Photo::with('fotografer')
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true)->withActiveSubscription())
            ->get();

        if ($photos->count() !== count($ids)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Ada foto yang sudah tidak tersedia. Hapus item tersebut sebelum melanjutkan checkout.']);
        }

        sort($ids);
        $pendingCheckout = session('pending_checkout_order');

        if (is_array($pendingCheckout) && ($pendingCheckout['photo_ids'] ?? []) === $ids) {
            $pendingOrder = PhotoOrder::where('order_id', $pendingCheckout['order_id'] ?? '')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($pendingOrder) {
                return $this->startPayment($pendingOrder, $doku);
            }
        }

        $order = $billing->createOrder($user, $photos);

        session()->put('pending_checkout_order', [
            'order_id' => $order->order_id,
            'photo_ids' => $ids,
        ]);

        return $this->startPayment($order, $doku);
    }

    public function paymentPage(Request $request, string $order): View
    {
        $photoOrder = $this->buyerPhotoOrder($request, $order);
        $transactions = $this->buyerOrderTransactions($request, $order);
        $summary = $this->orderSummary($order, $transactions);

        return view('payment', [
            'order_id' => $order,
            'transactions' => $transactions,
            'total' => $summary['total'],
            'paymentStatus' => $photoOrder->status,
            'qrContent' => $photoOrder->qr_content,
            'expiresAt' => $photoOrder->expires_at?->toIso8601String(),
        ]);
    }

    public function paymentStatus(Request $request, string $order): JsonResponse
    {
        $photoOrder = $this->buyerPhotoOrder($request, $order);

        return response()->json(['status' => $photoOrder->status]);
    }

    public function simulatePay(Request $request, string $order, PhotoOrderBilling $billing): RedirectResponse
    {
        $photoOrder = $this->buyerPhotoOrder($request, $order);
        $billing->markPaidLocally($photoOrder);
        $this->removePurchasedPhotosFromCart($this->buyerOrderTransactions($request, $order));

        return redirect()->route('checkout.success', ['order' => $order]);
    }

    public function checkoutSuccess(Request $request, string $order): View
    {
        $photoOrder = $this->buyerPhotoOrder($request, $order);
        $transactions = $this->buyerOrderTransactions($request, $order);
        abort_unless($photoOrder->status === 'paid', 404);
        $this->removePurchasedPhotosFromCart($transactions);

        return view('checkout-success', [
            'order' => $this->orderSummary($order, $transactions),
            'transactions' => $transactions,
        ]);
    }

    public function purchaseShow(Request $request, string $order): View
    {
        $transactions = $this->buyerOrderTransactions($request, $order);

        return view('purchases.show', [
            'order' => $this->orderSummary($order, $transactions),
            'transactions' => $transactions,
        ]);
    }

    /**
     * @return Collection<int, Transaction>
     */
    private function buyerOrderTransactions(Request $request, string $order): Collection
    {
        $query = Transaction::query()
            ->where('pembeli_id', $request->user()->id)
            ->with(['photo.event', 'photo.fotografer']);

        if (str_starts_with($order, 'LEGACY-')) {
            $query->whereKey((int) str($order)->after('LEGACY-')->toString());
        } else {
            $query->where('order_number', $order);
        }

        $transactions = $query
            ->orderBy('id')
            ->get();

        abort_if($transactions->isEmpty(), 404);

        return $transactions;
    }

    private function buyerPhotoOrder(Request $request, string $order): PhotoOrder
    {
        return PhotoOrder::where('order_id', $order)->where('user_id', $request->user()->id)->firstOrFail();
    }

    private function startPayment(PhotoOrder $order, DokuPaymentGateway $doku): RedirectResponse
    {
        if (! $order->qr_content) {
            try {
                $payment = $doku->createPhotoOrderPayment($order);
                $order->update(['provider_transaction_id' => $payment['reference'], 'provider_external_id' => $payment['external_id'], 'qr_content' => $payment['qr_content'], 'payment_method' => 'qris']);
            } catch (Throwable $exception) {
                report($exception);

                return redirect()->route('cart.index')->withErrors(['payment' => 'DOKU belum dapat membuat QRIS. Silakan coba kembali.']);
            }
        }

        return redirect()->route('checkout.payment', ['order' => $order->order_id]);
    }

    /** @param Collection<int, Transaction> $transactions */
    private function removePurchasedPhotosFromCart(Collection $transactions): void
    {
        $cart = session()->get('cart', []);
        foreach ($transactions as $transaction) {
            unset($cart[$transaction->photo_id]);
        }

        $cart === [] ? session()->forget('cart') : session()->put('cart', $cart);
        session()->forget('pending_checkout_order');
    }

    /**
     * @param  Collection<int, Transaction>  $items
     * @return array<string, mixed>
     */
    private function orderSummary(string $orderNumber, Collection $items): array
    {
        $first = $items->sortByDesc('created_at')->first();
        $statuses = $items->pluck('payment_status')->filter();
        $status = $statuses->isNotEmpty() && $statuses->every(fn (string $status): bool => $status === 'paid')
            ? 'paid'
            : ($statuses->first() ?: $first?->status ?: 'pending');

        return [
            'order_number' => $orderNumber,
            'status' => $status,
            'created_at' => $first?->created_at,
            'paid_at' => $items->pluck('paid_at')->filter()->sortDesc()->first(),
            'total' => $items->sum('total_bayar'),
            'count' => $items->count(),
            'items' => $items,
        ];
    }
}
