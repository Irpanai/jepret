<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = $this->cartItems();
        $total = collect($cart)->sum('price');

        return view('pembeli.cart', compact('cart', 'total'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'photo_id' => 'required|exists:photos,id',
        ]);

        $photo = Photo::with(['fotografer', 'event'])
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true))
            ->findOrFail($request->photo_id);

        $cart = session()->get('cart', []);

        if (! isset($cart[$photo->id])) {
            $cart[$photo->id] = $photo->id;
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Ditambahkan ke keranjang');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'photo_id' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->photo_id])) {
            unset($cart[$request->photo_id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Dihapus dari keranjang');
    }

    /**
     * @return array<int, array{id:int, price:int, event:string, fotografer:string, preview:string|null}>
     */
    private function cartItems(): array
    {
        $ids = array_values(session()->get('cart', []));

        if ($ids === []) {
            return [];
        }

        return Photo::with(['fotografer', 'event'])
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true))
            ->get()
            ->map(fn (Photo $photo): array => [
                'id' => $photo->id,
                'price' => (int) $photo->harga,
                'event' => $photo->event->nama_event ?? 'Event',
                'fotografer' => $photo->fotografer->name ?? 'Photographer',
                'preview' => $photo->file_watermark,
            ])
            ->all();
    }
}
