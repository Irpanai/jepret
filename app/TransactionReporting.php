<?php

namespace App;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionReporting
{
    public function query(Request $request, ?int $photographerId = null): Builder
    {
        $request->validate(['q' => 'nullable|string|max:200', 'from' => 'nullable|date_format:Y-m-d', 'to' => 'nullable|date_format:Y-m-d|after_or_equal:from']);

        return Transaction::query()->with(['pembeli', 'fotografer', 'photo.event'])
            ->when($photographerId !== null, fn (Builder $query) => $query->forPhotographer($photographerId))
            ->when($request->filled('from'), fn (Builder $query) => $query->whereDate('transactions.created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn (Builder $query) => $query->whereDate('transactions.created_at', '<=', $request->input('to')))
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(function (Builder $query) use ($term): void {
                    $query->where('transactions.id', 'like', $term)->orWhere('order_number', 'like', $term)
                        ->orWhereHas('pembeli', fn (Builder $buyer) => $buyer->where('name', 'like', $term)->orWhere('email', 'like', $term))
                        ->orWhereHas('fotografer', fn (Builder $user) => $user->where('name', 'like', $term)->orWhere('email', 'like', $term))
                        ->orWhereHas('photo', fn (Builder $photo) => $photo->where('title', 'like', $term)->orWhereHas('event', fn (Builder $event) => $event->where('nama_event', 'like', $term)));
                });
            });
    }

    public function totals(Builder $query): array
    {
        $count = (clone $query)->count();
        $paid = (clone $query)->paid()->toBase()->selectRaw('COUNT(*) AS paid_count, COALESCE(SUM(total_bayar),0) AS gmv, COALESCE(SUM('.Transaction::photographerSql().'),0) AS photographer, COALESCE(SUM('.Transaction::platformSql().'),0) AS platform, SUM(payment_fee_amount) AS fees')->first();

        return ['count' => $count] + (array) $paid;
    }

    public function daily(Builder $query): array
    {
        return (clone $query)->paid()->toBase()->selectRaw('DATE(COALESCE(paid_at, transactions.created_at)) AS day, COUNT(*) AS count, SUM(total_bayar) AS gmv, SUM('.Transaction::photographerSql().') AS photographer, SUM('.Transaction::platformSql().') AS platform')->groupByRaw('DATE(COALESCE(paid_at, transactions.created_at))')->orderBy('day')->get()->map(fn ($row) => (array) $row)->all();
    }

    public function csv(Builder $query, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($query): void {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['Transaction ID', 'Order', 'Created', 'Paid', 'Buyer', 'Buyer email', 'Photographer', 'Photo', 'Event', 'Location', 'Photo price', 'Tip', 'Photographer 90%', 'Platform 10%', 'Payment fee', 'Method', 'Status'], ',', '"', '');
            foreach ((clone $query)->orderBy('transactions.id')->lazy(200) as $transaction) {
                $row = [$transaction->id, $transaction->order_number, $transaction->created_at, $transaction->paid_at, $transaction->pembeli?->name, $transaction->pembeli?->email, $transaction->fotografer?->name, $transaction->photo?->title, $transaction->photo?->event?->nama_event, $transaction->photo?->event?->lokasi, $transaction->harga_foto, $transaction->tip_amount, $transaction->jumlah_fotografer, $transaction->jumlah_platform, $transaction->payment_fee_amount, $transaction->payment_method, $transaction->payment_status];
                fputcsv($stream, array_map(function ($value) {
                    $value = (string) $value;

                    return preg_match('/^[\s]*[=+@-]/u', $value) ? "'".$value : $value;
                }, $row), ',', '"', '');
            }
            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
