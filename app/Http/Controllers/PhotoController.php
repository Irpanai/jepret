<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Event;
use App\Models\Package;
use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PhotoController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::where('fotografer_id', $request->user()->id)->latest()->get();
        $cameras = Camera::where('fotografer_id', $request->user()->id)->orderBy('name')->get();
        $photos = Photo::with(['event', 'camera'])
            ->where('fotografer_id', $request->user()->id)
            ->latest()
            ->get();

        return view('fotografer.photos.index', compact('events', 'cameras', 'photos'));
    }

    public function create(Request $request): View
    {
        $events = Event::where('fotografer_id', $request->user()->id)->latest()->get();
        $cameras = Camera::where('fotografer_id', $request->user()->id)->orderBy('name')->get();

        return view('fotografer.photos.create', compact('events', 'cameras'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'new_folder' => 'nullable|string|max:255',
            'camera_id' => 'nullable|exists:cameras,id',
            'title' => 'nullable|string|max:255',
            'taken_at' => 'nullable|date',
            'daypart' => 'nullable|in:morning,afternoon,evening,night',
            'category' => 'nullable|string|max:100',
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'harga' => 'required|integer|min:0',
        ]);

        if (! $request->filled('event_id') && ! $request->filled('new_folder')) {
            return back()
                ->withErrors(['event_id' => 'Silakan pilih event yang sudah ada atau buat folder/event baru.'])
                ->withInput();
        }

        $user = $request->user();

        if ($request->filled('camera_id')) {
            Camera::where('fotografer_id', $user->id)->findOrFail($request->integer('camera_id'));
        }

        $package = $user->package ?? Package::where('nama_paket', 'Basic')->first();
        $file = $request->file('photo');
        $fileSizeMB = $file->getSize() / 1024 / 1024;
        $quotaMb = $package?->kuota_storage_mb ?? 5000;

        if (($user->storage_terpakai_mb + $fileSizeMB) > $quotaMb) {
            return back()->withErrors(['photo' => 'Kuota storage paket Anda tidak mencukupi.'])->withInput();
        }

        $event = $this->resolveEvent($request);
        $eventName = Str::slug($event->nama_event);

        $originalPath = $file->store('photos/original/'.$eventName, 'local');
        $watermarkPath = $this->storeWatermarkedPreview($file->path(), $eventName);

        Photo::create([
            'event_id' => $event->id,
            'fotografer_id' => $user->id,
            'camera_id' => $validated['camera_id'] ?? null,
            'title' => $validated['title'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'file_asli' => $originalPath,
            'file_watermark' => $watermarkPath,
            'harga' => $validated['harga'],
            'ai_tags' => json_encode(['marketplace', (string) Str::of($event->nama_event)->lower()]),
            'status' => 'active',
            'published_at' => now(),
            'taken_at' => $validated['taken_at'] ?? null,
            'daypart' => $validated['daypart'] ?? null,
            'category' => $validated['category'] ?? null,
            'original_filename' => $file->getClientOriginalName(),
            'file_size_mb' => round($fileSizeMB, 2),
        ]);

        $user->increment('storage_terpakai_mb', (int) ceil($fileSizeMB));

        return back()->with('success', 'Foto berhasil diunggah dan diterbitkan ke marketplace.');
    }

    public function destroy(Photo $photo, Request $request): RedirectResponse
    {
        if ($photo->fotografer_id !== $request->user()->id) {
            abort(403);
        }

        try {
            $fileSizeMB = Storage::disk('local')->size($photo->file_asli) / 1024 / 1024;
            $request->user()->decrement('storage_terpakai_mb', (int) ceil($fileSizeMB));
        } catch (\Throwable) {
            // File may already be missing; keep deletion idempotent.
        }

        Storage::disk('local')->delete($photo->file_asli);
        Storage::disk('public')->delete($photo->file_watermark);

        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    private function resolveEvent(Request $request): Event
    {
        if ($request->filled('new_folder')) {
            return Event::firstOrCreate(
                [
                    'nama_event' => trim($request->string('new_folder')->toString()),
                    'fotografer_id' => $request->user()->id,
                ],
                [
                    'tanggal_event' => now(),
                    'lokasi' => 'Online',
                ]
            );
        }

        return Event::where('fotografer_id', $request->user()->id)->findOrFail($request->integer('event_id'));
    }

    private function storeWatermarkedPreview(string $sourcePath, string $eventName): string
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($sourcePath);

        $image->text('JEPRET', $image->width() / 2, $image->height() / 2, function ($font): void {
            $font->size(48);
            $font->color('rgba(255, 255, 255, 0.55)');
            $font->align('center');
            $font->valign('middle');
        });

        $watermarkDirectory = 'photos/watermark/'.$eventName;
        $watermarkPath = $watermarkDirectory.'/'.Str::uuid().'.jpg';

        Storage::disk('public')->makeDirectory($watermarkDirectory);
        $image->save(storage_path('app/public/'.$watermarkPath), quality: 82);

        return $watermarkPath;
    }
}
