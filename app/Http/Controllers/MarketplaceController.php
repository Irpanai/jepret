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
        $query = Photo::query()
            ->select([
                'id',
                'event_id',
                'fotografer_id',
                'camera_id',
                'title',
                'harga',
                'status',
                'resolusi',
                'published_at',
                'taken_at',
                'created_at',
                'category',
                'daypart',
            ])
            ->with([
                'fotografer:id,name,studio_name,avatar,profile_photo_path,slug',
                'event:id,nama_event,lokasi,tanggal_event',
            ])
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true)->withActiveSubscription());

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();
            $query->where(function ($photoQuery) use ($search): void {
                $photoQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('ai_tags', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($eventQuery) use ($search): void {
                        $eventQuery
                            ->where('nama_event', 'like', "%{$search}%")
                            ->orWhere('lokasi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('fotografer', function ($userQuery) use ($search): void {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('studio_name', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%");
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
            $query->where(function ($locationQuery) use ($location): void {
                $locationQuery
                    ->whereHas('event', fn ($eventQuery) => $eventQuery->where('lokasi', 'like', "%{$location}%"))
                    ->orWhereHas('fotografer', fn ($userQuery) => $userQuery->where('location', 'like', "%{$location}%"));
            });
        }

        if ($request->filled('date')) {
            $date = $request->date('date')?->format('Y-m-d') ?: (string) $request->input('date');
            $driver = $query->getConnection()->getDriverName();
            $witaDateExpr = $driver === 'sqlite'
                ? "date(datetime(COALESCE(photos.taken_at, photos.created_at), '+8 hours'))"
                : "DATE(DATE_ADD(COALESCE(photos.taken_at, photos.created_at), INTERVAL 8 HOUR))";

            $query->where(function ($dateQuery) use ($date, $witaDateExpr): void {
                $dateQuery->whereHas('event', fn ($eventQuery) => $eventQuery->whereDate('tanggal_event', $date))
                    ->orWhereRaw("{$witaDateExpr} = ?", [$date]);
            });
        }

        if ($request->filled('daypart')) {
            $daypart = strtolower($request->string('daypart')->toString());
            $driver = $query->getConnection()->getDriverName();
            $hourExpr = $driver === 'sqlite'
                ? "CAST(strftime('%H', datetime(COALESCE(photos.taken_at, photos.created_at), '+8 hours')) AS INTEGER)"
                : "HOUR(DATE_ADD(COALESCE(photos.taken_at, photos.created_at), INTERVAL 8 HOUR))";

            $query->where(function ($daypartQuery) use ($daypart, $hourExpr): void {
                $daypartQuery->where('photos.daypart', $daypart);

                match ($daypart) {
                    'morning' => $daypartQuery->orWhereRaw("{$hourExpr} BETWEEN 5 AND 11"),
                    'afternoon' => $daypartQuery->orWhereRaw("{$hourExpr} BETWEEN 12 AND 15"),
                    'evening' => $daypartQuery->orWhereRaw("{$hourExpr} BETWEEN 16 AND 18"),
                    'night' => $daypartQuery->orWhereRaw("({$hourExpr} >= 19 OR {$hourExpr} < 5)"),
                    default => null,
                };
            });
        }

        match ($request->string('sort')->toString()) {
            'oldest' => $query->orderBy('published_at')->orderBy('created_at'),
            'price_low' => $query->orderBy('harga'),
            'price_high' => $query->orderByDesc('harga'),
            default => $query->orderByDesc('published_at')->orderByDesc('created_at'),
        };

        $photos = $query
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        $photographers = User::where('role', 'fotografer')
            ->where('is_verified', true)
            ->where('is_active', true)
            ->withActiveSubscription()
            ->orderBy('name')
            ->get(['id', 'name', 'studio_name']);

        $events = Event::whereHas('photos.fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true)->withActiveSubscription())
            ->orderByDesc('tanggal_event')
            ->get(['id', 'nama_event', 'lokasi', 'tanggal_event']);

        $eventLocations = Event::query()
            ->whereHas('photos.fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true)->withActiveSubscription())
            ->whereNotNull('lokasi')
            ->where('lokasi', '!=', '')
            ->where('lokasi', '!=', 'Online')
            ->pluck('lokasi');

        $photographerLocations = User::query()
            ->where('role', 'fotografer')
            ->where('is_verified', true)
            ->where('is_active', true)
            ->withActiveSubscription()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->pluck('location');

        $locations = $eventLocations->concat($photographerLocations)
            ->map(fn ($location) => trim((string) $location))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $categories = Photo::query()
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true)->withActiveSubscription())
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('galeri', compact('photos', 'photographers', 'events', 'locations', 'categories'));
    }

    public function show(Photo $photo): View
    {
        abort_unless($photo->status === 'active' && $photo->fotografer()->where('is_verified', true)->where('is_active', true)->withActiveSubscription()->exists(), 404);
        $photo->load(['fotografer.cameras', 'event', 'camera']);
        $photo->increment('views_count');

        $relatedPhotos = Photo::where('event_id', $photo->event_id)
            ->where('fotografer_id', $photo->fotografer_id)
            ->where('id', '!=', $photo->id)
            ->where('status', 'active')
            ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true)->withActiveSubscription())
            ->with(['event', 'fotografer'])
            ->limit(4)
            ->get();

        return view('marketplace.show', compact('photo', 'relatedPhotos'));
    }

    public function checkout(Photo $photo): View
    {
        $photo->load(['fotografer', 'event']);
        abort_unless($photo->status === 'active' && $photo->fotografer?->is_verified && $photo->fotografer?->is_active && $photo->fotografer?->hasActiveSubscription(), 404);

        return view('marketplace.checkout', compact('photo'));
    }
}
