<?php

namespace App;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionReporting
{
    public function query(Request $request, ?int $photographerId = null): Builder
    {
        $request->validate(['q' => 'nullable|string|max:200', 'from' => 'nullable|date_format:Y-m-d', 'to' => 'nullable|date_format:Y-m-d|after_or_equal:from']);
        $from = $request->filled('from')
            ? Carbon::parse($request->string('from')->toString(), config('app.display_timezone'))->startOfDay()->utc()
            : null;
        $to = $request->filled('to')
            ? Carbon::parse($request->string('to')->toString(), config('app.display_timezone'))->endOfDay()->utc()
            : null;

        return Transaction::query()->with(['pembeli', 'fotografer', 'photo.event'])
            ->when($photographerId !== null, fn (Builder $query) => $query->forPhotographer($photographerId))
            ->when($from, fn (Builder $query) => $query->where('transactions.created_at', '>=', $from))
            ->when($to, fn (Builder $query) => $query->where('transactions.created_at', '<=', $to))
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

    /**
     * Generate structured chart data for realtime transaction analytics.
     * Periods: 'hari' (hours of today), 'tanggal' (last 30 days), 'bulan' (last 12 months).
     */
    public function chartData(string $period = 'tanggal', ?int $photographerId = null): array
    {
        $timezone = config('app.display_timezone', 'Asia/Makassar');
        $now = Carbon::now($timezone);

        if ($period === 'hari') {
            $start = (clone $now)->startOfDay()->utc();
            $end = (clone $now)->endOfDay()->utc();

            $transactions = Transaction::query()
                ->paid()
                ->when($photographerId !== null, fn (Builder $q) => $q->forPhotographer($photographerId))
                ->where(function ($q) use ($start, $end): void {
                    $q->where(function ($sub) use ($start, $end): void {
                        $sub->whereNotNull('paid_at')->whereBetween('paid_at', [$start, $end]);
                    })->orWhere(function ($sub) use ($start, $end): void {
                        $sub->whereNull('paid_at')->whereBetween('transactions.created_at', [$start, $end]);
                    });
                })
                ->selectRaw('COALESCE(paid_at, transactions.created_at) as tx_time, total_bayar, ('.Transaction::photographerSql().') as fotografer_amount')
                ->get();

            $points = [];
            $totalCount = 0;
            $totalGmv = 0;
            $totalEarnings = 0;

            for ($hour = 0; $hour < 24; $hour++) {
                $points[$hour] = [
                    'label' => sprintf('%02d:00', $hour),
                    'full_label' => sprintf('Pukul %02d:00 - %02d:59', $hour, $hour),
                    'count' => 0,
                    'gmv' => 0,
                    'gmv_formatted' => 'Rp0',
                    'earnings' => 0,
                    'earnings_formatted' => 'Rp0',
                ];
            }

            foreach ($transactions as $tx) {
                $txHour = (int) Carbon::parse($tx->tx_time)->timezone($timezone)->format('G');
                if (isset($points[$txHour])) {
                    $points[$txHour]['count']++;
                    $points[$txHour]['gmv'] += (int) $tx->total_bayar;
                    $points[$txHour]['gmv_formatted'] = 'Rp'.number_format($points[$txHour]['gmv'], 0, ',', '.');
                    $points[$txHour]['earnings'] += (int) $tx->fotografer_amount;
                    $points[$txHour]['earnings_formatted'] = 'Rp'.number_format($points[$txHour]['earnings'], 0, ',', '.');
                    $totalCount++;
                    $totalGmv += (int) $tx->total_bayar;
                    $totalEarnings += (int) $tx->fotografer_amount;
                }
            }

            return [
                'period' => 'hari',
                'title' => 'Hari Ini ('.$now->translatedFormat('d M Y').')',
                'total_count' => $totalCount,
                'total_gmv' => $totalGmv,
                'total_gmv_formatted' => 'Rp'.number_format($totalGmv, 0, ',', '.'),
                'total_earnings' => $totalEarnings,
                'total_earnings_formatted' => 'Rp'.number_format($totalEarnings, 0, ',', '.'),
                'updated_at' => $now->format('H:i:s').' WITA',
                'points' => array_values($points),
            ];
        }

        if ($period === 'bulan') {
            $start = (clone $now)->subMonths(11)->startOfMonth()->utc();
            $end = (clone $now)->endOfMonth()->utc();

            $transactions = Transaction::query()
                ->paid()
                ->when($photographerId !== null, fn (Builder $q) => $q->forPhotographer($photographerId))
                ->where(function ($q) use ($start, $end): void {
                    $q->where(function ($sub) use ($start, $end): void {
                        $sub->whereNotNull('paid_at')->whereBetween('paid_at', [$start, $end]);
                    })->orWhere(function ($sub) use ($start, $end): void {
                        $sub->whereNull('paid_at')->whereBetween('transactions.created_at', [$start, $end]);
                    });
                })
                ->selectRaw('COALESCE(paid_at, transactions.created_at) as tx_time, total_bayar, ('.Transaction::photographerSql().') as fotografer_amount')
                ->get();

            $points = [];
            $totalCount = 0;
            $totalGmv = 0;
            $totalEarnings = 0;

            for ($i = 11; $i >= 0; $i--) {
                $date = (clone $now)->subMonths($i);
                $key = $date->format('Y-m');
                $points[$key] = [
                    'label' => $date->translatedFormat('M y'),
                    'full_label' => 'Bulan '.$date->translatedFormat('F Y'),
                    'count' => 0,
                    'gmv' => 0,
                    'gmv_formatted' => 'Rp0',
                    'earnings' => 0,
                    'earnings_formatted' => 'Rp0',
                ];
            }

            foreach ($transactions as $tx) {
                $key = Carbon::parse($tx->tx_time)->timezone($timezone)->format('Y-m');
                if (isset($points[$key])) {
                    $points[$key]['count']++;
                    $points[$key]['gmv'] += (int) $tx->total_bayar;
                    $points[$key]['gmv_formatted'] = 'Rp'.number_format($points[$key]['gmv'], 0, ',', '.');
                    $points[$key]['earnings'] += (int) $tx->fotografer_amount;
                    $points[$key]['earnings_formatted'] = 'Rp'.number_format($points[$key]['earnings'], 0, ',', '.');
                    $totalCount++;
                    $totalGmv += (int) $tx->total_bayar;
                    $totalEarnings += (int) $tx->fotografer_amount;
                }
            }

            return [
                'period' => 'bulan',
                'title' => '12 Bulan Terakhir',
                'total_count' => $totalCount,
                'total_gmv' => $totalGmv,
                'total_gmv_formatted' => 'Rp'.number_format($totalGmv, 0, ',', '.'),
                'total_earnings' => $totalEarnings,
                'total_earnings_formatted' => 'Rp'.number_format($totalEarnings, 0, ',', '.'),
                'updated_at' => $now->format('H:i:s').' WITA',
                'points' => array_values($points),
            ];
        }

        // Default: 'tanggal' (30 hari terakhir)
        $start = (clone $now)->subDays(29)->startOfDay()->utc();
        $end = (clone $now)->endOfDay()->utc();

        $transactions = Transaction::query()
            ->paid()
            ->when($photographerId !== null, fn (Builder $q) => $q->forPhotographer($photographerId))
            ->where(function ($q) use ($start, $end): void {
                $q->where(function ($sub) use ($start, $end): void {
                    $sub->whereNotNull('paid_at')->whereBetween('paid_at', [$start, $end]);
                })->orWhere(function ($sub) use ($start, $end): void {
                    $sub->whereNull('paid_at')->whereBetween('transactions.created_at', [$start, $end]);
                });
            })
            ->selectRaw('COALESCE(paid_at, transactions.created_at) as tx_time, total_bayar, ('.Transaction::photographerSql().') as fotografer_amount')
            ->get();

        $points = [];
        $totalCount = 0;
        $totalGmv = 0;
        $totalEarnings = 0;

        for ($i = 29; $i >= 0; $i--) {
            $date = (clone $now)->subDays($i);
            $key = $date->format('Y-m-d');
            $points[$key] = [
                'label' => $date->format('d M'),
                'full_label' => 'Tanggal '.$date->translatedFormat('d F Y'),
                'count' => 0,
                'gmv' => 0,
                'gmv_formatted' => 'Rp0',
                'earnings' => 0,
                'earnings_formatted' => 'Rp0',
            ];
        }

        foreach ($transactions as $tx) {
            $key = Carbon::parse($tx->tx_time)->timezone($timezone)->format('Y-m-d');
            if (isset($points[$key])) {
                $points[$key]['count']++;
                $points[$key]['gmv'] += (int) $tx->total_bayar;
                $points[$key]['gmv_formatted'] = 'Rp'.number_format($points[$key]['gmv'], 0, ',', '.');
                $points[$key]['earnings'] += (int) $tx->fotografer_amount;
                $points[$key]['earnings_formatted'] = 'Rp'.number_format($points[$key]['earnings'], 0, ',', '.');
                $totalCount++;
                $totalGmv += (int) $tx->total_bayar;
                $totalEarnings += (int) $tx->fotografer_amount;
            }
        }

        return [
            'period' => 'tanggal',
            'title' => '30 Hari Terakhir',
            'total_count' => $totalCount,
            'total_gmv' => $totalGmv,
            'total_gmv_formatted' => 'Rp'.number_format($totalGmv, 0, ',', '.'),
            'total_earnings' => $totalEarnings,
            'total_earnings_formatted' => 'Rp'.number_format($totalEarnings, 0, ',', '.'),
            'updated_at' => $now->format('H:i:s').' WITA',
            'points' => array_values($points),
        ];
    }

    public function csv(Builder $query, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($query): void {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['Transaction ID', 'Order', 'Created', 'Paid', 'Buyer', 'Buyer email', 'Photographer', 'Photo', 'Event', 'Location', 'Photo price', 'Tip', 'Photographer 90%', 'Platform 10%', 'Payment fee', 'Method', 'Status'], ',', '"', '');
            foreach ((clone $query)->orderBy('transactions.id')->lazy(200) as $transaction) {
                $row = [$transaction->id, $transaction->order_number, $transaction->created_at?->timezone(config('app.display_timezone')), $transaction->paid_at?->timezone(config('app.display_timezone')), $transaction->pembeli?->name, $transaction->pembeli?->email, $transaction->fotografer?->name, $transaction->photo?->title, $transaction->photo?->event?->nama_event, $transaction->photo?->event?->lokasi, $transaction->harga_foto, $transaction->tip_amount, $transaction->jumlah_fotografer, $transaction->jumlah_platform, $transaction->payment_fee_amount, $transaction->payment_method, $transaction->payment_status];
                fputcsv($stream, array_map(function ($value) {
                    $value = (string) $value;

                    return preg_match('/^[\s]*[=+@-]/u', $value) ? "'".$value : $value;
                }, $row), ',', '"', '');
            }
            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
