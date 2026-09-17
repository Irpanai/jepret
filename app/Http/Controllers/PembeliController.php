<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Transaction;
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
        $ids = array_values(session()->get('cart', []));

        if ($ids === []) {
            return redirect()->route('galeri')->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        $photos = Photo::with('fotografer')
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->get();

        if ($photos->isEmpty()) {
            session()->forget('cart');

            return redirect()->route('galeri')->withErrors(['cart' => 'Foto di keranjang tidak tersedia.']);
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

        session()->forget('cart');

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
        $transactions = Transaction::where('order_number', $order)
            ->where('pembeli_id', $request->user()->id)
            ->where('payment_status', 'pending')
            ->with('fotografer')
            ->get();

        abort_if($transactions->isEmpty(), 404);

        DB::transaction(function () use ($transactions): void {
            foreach ($transactions as $transaction) {
                $transaction->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'payment_reference' => 'LOCAL-'.Str::upper(Str::random(10)),
                ]);

                if ($transaction->fotografer) {
                    $transaction->fotografer->increment('saldo', $transaction->jumlah_fotografer);
                }
            }
        });

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
