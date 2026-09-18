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
        $this->authorizePurchase($request, $order, $transaction);

        $filename = $transaction->photo->original_filename ?: basename($transaction->photo->file_asli);

        return Storage::disk('local')->download($transaction->photo->purchased_path, $filename);
    }

    public function preview(Request $request, string $order, Transaction $transaction): StreamedResponse
    {
        $this->authorizePurchase($request, $order, $transaction);

        return Storage::disk('local')->response($transaction->photo->purchased_path);
    }

    private function authorizePurchase(Request $request, string $order, Transaction $transaction): void
    {
        abort_if($transaction->pembeli_id !== $request->user()->id, 403);
        $belongsToOrder = str_starts_with($order, 'LEGACY-')
            ? $transaction->order_number === null && $transaction->id === (int) str($order)->after('LEGACY-')->toString()
            : $transaction->order_number === $order;
        abort_unless($belongsToOrder, 403);
        abort_if($transaction->payment_status !== 'paid' || $transaction->status !== 'paid', 403);
        $transaction->loadMissing('photo');
        abort_if(! $transaction->photo?->purchased_path || ! Storage::disk('local')->exists($transaction->photo->purchased_path), 404);
    }
}
