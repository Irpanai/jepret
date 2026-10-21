<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Photo;
use App\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherPreviewController extends Controller
{
    public function __invoke(Request $request, VoucherService $vouchers): JsonResponse
    {
        $data = $request->validate(['voucher_code' => ['required', 'string', 'max:50'], 'scope' => ['required', 'in:photo,subscription'], 'package_id' => ['nullable', 'integer']]);
        if ($data['scope'] === 'subscription') {
            $package = Package::query()->whereKey($data['package_id'])->where('is_active', true)->firstOrFail();
            $subtotal = (int) $package->harga;
            $platformCommission = null;
        } else {
            $photos = Photo::whereIn('id', array_values(session('cart', [])))->where('status', 'active')->get();
            $subtotal = (int) $photos->sum('harga');
            $platformCommission = (int) $photos->sum(fn (Photo $photo): int => Photo::platformAmount((int) $photo->harga));
        }
        $preview = $vouchers->preview($data['voucher_code'], $data['scope'], $subtotal, $request->user(), $platformCommission, $package ?? null);

        return response()->json(['code' => $preview['voucher']->code, 'subtotal' => $subtotal, 'discount' => $preview['discount'], 'final' => $preview['final']]);
    }
}
