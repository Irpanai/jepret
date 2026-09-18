<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true))
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

    public function processCheckout(Request $request): RedirectResponse
    {
        $user = $request->user();
        $ids = array_values(array_unique(array_map('intval', session()->get('cart', []))));

        if ($ids === []) {
            return redirect()->route('galeri')->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        $photos = Photo::with('fotografer')
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true))
            ->get();

        if ($photos->count() !== count($ids)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Ada foto yang sudah tidak tersedia. Hapus item tersebut sebelum melanjutkan checkout.']);
        }

        sort($ids);
        $pendingCheckout = session('pending_checkout_order');

        if (is_array($pendingCheckout) && ($pendingCheckout['photo_ids'] ?? []) === $ids) {
            $pendingOrderExists = Transaction::where('order_number', $pendingCheckout['order_id'] ?? '')
                ->where('pembeli_id', $user->id)
                ->where('payment_status', 'pending')
                ->exists();

            if ($pendingOrderExists) {
                return redirect()->route('checkout.payment', ['order' => $pendingCheckout['order_id']]);
            }
        }

        $orderId = 'JEPRET-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5));

        DB::transaction(function () use ($photos, $user, $orderId): void {
            foreach ($photos as $photo) {
                $price = (int) $photo->harga;
                $photographerAmount = Photo::photographerAmount($price);
                $platformAmount = Photo::platformAmount($price);

                Transaction::create([
                    'order_number' => $orderId,
                    'pembeli_id' => $user->id,
                    'photo_id' => $photo->id,
                    'fotografer_id' => $photo->fotografer_id,
                    'harga_foto' => $price,
                    'tip_amount' => 0,
                    'total_bayar' => $price,
                    'photographer_amount' => $photographerAmount,
                    'platform_amount' => $platformAmount,
                    'revenue_share_snapshot' => [
                        'photographer_percent' => Photo::PHOTOGRAPHER_SHARE_PERCENT,
                        'platform_percent' => Photo::PLATFORM_SHARE_PERCENT,
                        'photographer_name' => $photo->fotografer->name ?? null,
                    ],
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => 'qris',
                    'expires_at' => now()->addHour(),
                ]);
            }
        });

        session()->put('pending_checkout_order', [
            'order_id' => $orderId,
            'photo_ids' => $ids,
        ]);

        return redirect()->route('checkout.payment', ['order' => $orderId]);
    }

    public function paymentPage(Request $request, string $order): View
    {
        $transactions = $this->buyerOrderTransactions($request, $order);
        $summary = $this->orderSummary($order, $transactions);

        return view('payment', [
            'order_id' => $order,
            'transactions' => $transactions,
            'total' => $summary['total'],
            'paymentStatus' => $summary['status'],
        ]);
    }

    public function simulatePay(Request $request, string $order): RedirectResponse
    {
        $purchasedPhotoIds = DB::transaction(function () use ($request, $order): array {
            $transactions = Transaction::where('order_number', $order)
                ->where('pembeli_id', $request->user()->id)->lockForUpdate()->get();
            abort_if($transactions->isEmpty(), 404);
            foreach ($transactions->where('payment_status', 'pending') as $transaction) {
                $transaction->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'payment_reference' => 'LOCAL-'.Str::upper(Str::random(10)),
                ]);

                User::whereKey($transaction->fotografer_id)->lockForUpdate()->increment('saldo', $transaction->jumlah_fotografer);
            }

            return $transactions->pluck('photo_id')->map(fn ($id): int => (int) $id)->all();
        });

        $cart = session()->get('cart', []);
        foreach ($purchasedPhotoIds as $photoId) {
            unset($cart[$photoId]);
        }

        if ($cart === []) {
            session()->forget('cart');
        } else {
            session()->put('cart', $cart);
        }
        session()->forget('pending_checkout_order');

        return redirect()->route('checkout.success', ['order' => $order]);
    }

    public function checkoutSuccess(Request $request, string $order): View
    {
        $transactions = $this->buyerOrderTransactions($request, $order);
        abort_unless($this->orderSummary($order, $transactions)['status'] === 'paid', 404);

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
