<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Event;

class MarketplaceController extends Controller
{
    public function galeri(Request $request)
    {
        // Default query
        $query = Photo::with(['fotografer', 'event']);

        // Implement simple filter/search if needed
        if ($request->has('q')) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            });
        }

        $photos = $query->latest()->paginate(20);

        return view('galeri', compact('photos'));
    }

    public function show(Photo $photo)
    {
        $photo->load(['fotografer', 'event']);
        
        $relatedPhotos = Photo::where('event_id', $photo->event_id)
            ->where('fotografer_id', $photo->fotografer_id)
            ->where('id', '!=', $photo->id)
            ->limit(4)
            ->get();

        return view('marketplace.show', compact('photo', 'relatedPhotos'));
    }

    public function checkout(Photo $photo)
    {
        $photo->load(['fotografer', 'event']);
        return view('marketplace.checkout', compact('photo'));
    }
}
