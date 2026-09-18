<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function galeri(Request $request): View
    {
        $query = Photo::with(['fotografer', 'event'])
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true));

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();
            $query->where(function ($photoQuery) use ($search): void {
                $photoQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('ai_tags', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($eventQuery) use ($search): void {
                        $eventQuery
                            ->where('nama_event', 'like', "%{$search}%")
                            ->orWhere('lokasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('fotografer', function ($userQuery) use ($search): void {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('studio_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('photographer')) {
            $query->where('fotografer_id', $request->integer('photographer'));
        }

        if ($request->filled('event')) {
            $query->where('event_id', $request->integer('event'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category')->toString());
        }

        if ($request->filled('location')) {
            $location = $request->string('location')->toString();
            $query->whereHas('event', fn ($eventQuery) => $eventQuery->where('lokasi', 'like', "%{$location}%"));
        }

        if ($request->filled('date')) {
            $query->whereHas('event', fn ($eventQuery) => $eventQuery->whereDate('tanggal_event', $request->date('date')));
        }

        if ($request->filled('daypart')) {
            $query->where('daypart', $request->string('daypart')->toString());
        }

        match ($request->string('sort')->toString()) {
            'oldest' => $query->orderBy('published_at')->orderBy('created_at'),
            'price_low' => $query->orderBy('harga'),
            'price_high' => $query->orderByDesc('harga'),
            default => $query->orderByDesc('published_at')->orderByDesc('created_at'),
        };

        $photos = $query
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $photographers = User::where('role', 'fotografer')->where('is_verified', true)->orderBy('name')->get(['id', 'name', 'studio_name']);
        $events = Event::whereHas('photos.fotografer', fn ($query) => $query->where('is_verified', true))->orderByDesc('tanggal_event')->get(['id', 'nama_event', 'lokasi', 'tanggal_event']);
        $locations = Event::query()->whereHas('photos.fotografer', fn ($query) => $query->where('is_verified', true))->whereNotNull('lokasi')->distinct()->orderBy('lokasi')->pluck('lokasi');
        $categories = Photo::query()->whereHas('fotografer', fn ($query) => $query->where('is_verified', true))->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('galeri', compact('photos', 'photographers', 'events', 'locations', 'categories'));
    }

    public function show(Photo $photo): View
    {
        abort_unless($photo->status === 'active' && $photo->fotografer()->where('is_verified', true)->exists(), 404);
        $photo->load(['fotografer', 'event']);
        $photo->increment('views_count');

        $relatedPhotos = Photo::where('event_id', $photo->event_id)
            ->where('fotografer_id', $photo->fotografer_id)
            ->where('id', '!=', $photo->id)
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true))
            ->with(['event', 'fotografer'])
            ->limit(4)
            ->get();

        return view('marketplace.show', compact('photo', 'relatedPhotos'));
    }

    public function checkout(Photo $photo): View
    {
        $photo->load(['fotografer', 'event']);
        abort_unless($photo->status === 'active' && $photo->fotografer?->is_verified, 404);

        return view('marketplace.checkout', compact('photo'));
    }
}
