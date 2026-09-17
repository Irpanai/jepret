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
            ->where('status', 'active');

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

        $photos = $query
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $photographers = User::where('role', 'fotografer')->orderBy('name')->get(['id', 'name', 'studio_name']);
        $events = Event::orderByDesc('tanggal_event')->get(['id', 'nama_event', 'lokasi', 'tanggal_event']);
        $categories = Photo::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('galeri', compact('photos', 'photographers', 'events', 'categories'));
    }

    public function show(Photo $photo): View
    {
        $photo->load(['fotografer', 'event']);
        $photo->increment('views_count');

        $relatedPhotos = Photo::where('event_id', $photo->event_id)
            ->where('fotografer_id', $photo->fotografer_id)
            ->where('id', '!=', $photo->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view('marketplace.show', compact('photo', 'relatedPhotos'));
    }

    public function checkout(Photo $photo): View
    {
        $photo->load(['fotografer', 'event']);

        return view('marketplace.checkout', compact('photo'));
    }
}
