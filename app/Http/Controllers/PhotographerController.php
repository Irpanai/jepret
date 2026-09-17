<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhotographerController extends Controller
{
    public function index(Request $request): View
    {
        $photographers = $this->photographerQuery($request)
            ->with('featuredPhoto.event')
            ->withCount([
                'photos as active_photos_count' => fn ($query) => $query->where('status', 'active'),
                'events',
                'photographerTransactions as paid_sales_count' => fn ($query) => $query->where('payment_status', 'paid'),
            ])
            ->orderByDesc('active_photos_count')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $featuredPhotographers = User::query()
            ->where('role', 'fotografer')
            ->with('featuredPhoto.event')
            ->withCount([
                'photos as active_photos_count' => fn ($query) => $query->where('status', 'active'),
                'photographerTransactions as paid_sales_count' => fn ($query) => $query->where('payment_status', 'paid'),
            ])
            ->orderByDesc('paid_sales_count')
            ->orderByDesc('active_photos_count')
            ->limit(3)
            ->get();

        $locations = User::query()
            ->where('role', 'fotografer')
            ->whereNotNull('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $categories = User::query()
            ->where('role', 'fotografer')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('photographers.index', compact('photographers', 'featuredPhotographers', 'locations', 'categories'));
    }

    public function show(Request $request, string $photographer): View
    {
        $photographer = User::query()
            ->where('role', 'fotografer')
            ->where(function ($query) use ($photographer): void {
                $query->where('slug', $photographer);

                if (ctype_digit($photographer)) {
                    $query->orWhereKey((int) $photographer);
                }
            })
            ->withCount([
                'photos as active_photos_count' => fn ($query) => $query->where('status', 'active'),
                'events',
                'photographerTransactions as paid_sales_count' => fn ($query) => $query->where('payment_status', 'paid'),
            ])
            ->firstOrFail();

        $featuredPhotos = Photo::query()
            ->whereBelongsTo($photographer, 'fotografer')
            ->where('status', 'active')
            ->with('event')
            ->orderByDesc('views_count')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $photos = Photo::query()
            ->whereBelongsTo($photographer, 'fotografer')
            ->where('status', 'active')
            ->with('event')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('photographers.show', compact('photographer', 'featuredPhotos', 'photos'));
    }

    private function photographerQuery(Request $request): Builder
    {
        return User::query()
            ->where('role', 'fotografer')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = $request->string('q')->toString();

                $query->where(function ($userQuery) use ($search): void {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('studio_name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('location'), function ($query) use ($request): void {
                $query->where('location', $request->string('location')->toString());
            })
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->where('category', $request->string('category')->toString());
            });
    }
}
