<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseDownloadController extends Controller
{
    public function __invoke(Request $request, string $order, Transaction $transaction): StreamedResponse
    {
        abort_if($transaction->pembeli_id !== $request->user()->id, 403);

        $belongsToOrder = str_starts_with($order, 'LEGACY-')
            ? $transaction->order_number === null && $transaction->id === (int) str($order)->after('LEGACY-')->toString()
            : $transaction->order_number === $order;

        abort_unless($belongsToOrder, 403);
        abort_if($transaction->payment_status !== 'paid' || $transaction->status !== 'paid', 403);

        $transaction->loadMissing('photo');

        abort_if(! $transaction->photo, 404);
        abort_if(! Storage::disk('local')->exists($transaction->photo->file_asli), 404);

        $filename = $transaction->photo->original_filename ?: basename($transaction->photo->file_asli);

        return Storage::disk('local')->download($transaction->photo->file_asli, $filename);
    }
}
