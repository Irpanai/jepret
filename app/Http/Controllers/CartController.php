<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;

class CartController extends Controller
{
    public function index()
    {
        // Mocking cart for UI purposes.
        $cart = session()->get('cart', []);
        return response()->json($cart);
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo_id' => 'required|exists:photos,id'
        ]);

        $cart = session()->get('cart', []);
        
        if(!isset($cart[$request->photo_id])) {
            $photo = Photo::with('fotografer.user')->find($request->photo_id);
            $cart[$request->photo_id] = [
                'id' => $photo->id,
                'price' => $photo->price,
                'event' => $photo->event->name ?? 'Event',
                'fotografer' => $photo->fotografer->user->name ?? 'Photographer',
                'url' => $photo->url
            ];
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Ditambahkan ke keranjang');
    }

    public function destroy(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
        return redirect()->back()->with('success', 'Dihapus dari keranjang');
    }
}
