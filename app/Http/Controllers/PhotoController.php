<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Event;
use App\Models\Photo;
use App\PhotoProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class PhotoController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::where('fotografer_id', $request->user()->id)->withCount('photos')->latest()->get();
        $cameras = Camera::where('fotografer_id', $request->user()->id)->orderBy('name')->get();
        $photos = Photo::with(['event', 'camera'])->where('fotografer_id', $request->user()->id)->latest()->paginate(18);

        return view('fotografer.photos.index', compact('events', 'cameras', 'photos'));
    }

    public function create(Request $request): View
    {
        $events = Event::where('fotografer_id', $request->user()->id)->latest()->get();
        $cameras = Camera::where('fotografer_id', $request->user()->id)->orderBy('name')->get();

        return view('fotografer.photos.create', compact('events', 'cameras'));
    }

    public function store(Request $request, PhotoProcessor $processor): RedirectResponse
    {
        abort_unless($request->user()->is_verified, 403, 'Akun photographer harus disetujui sebelum mengunggah foto.');
        $validated = $request->validate([
            'event_id' => ['nullable', 'integer'], 'new_folder' => ['nullable', 'string', 'max:255'],
            'camera_id' => ['nullable', 'integer'], 'title' => ['nullable', 'string', 'max:255'],
            'taken_at' => ['nullable', 'date'], 'daypart' => ['nullable', 'in:morning,afternoon,evening,night'],
            'category' => ['nullable', 'string', 'max:100'], 'harga' => ['required', 'integer', 'min:0'],
            'photos' => ['required', 'array', 'min:1'], 'photos.*' => ['required', File::types(['jpg', 'jpeg', 'png', 'webp', 'mov'])],
            'watermark' => ['nullable', File::image()->types(['png', 'webp'])->max('5mb')], 'lock_watermark' => ['nullable', 'boolean'],
        ]);
        $user = $request->user()->load('package');
        $event = $this->resolveEvent($request);
        if ($request->filled('camera_id')) {
            Camera::where('fotografer_id', $user->id)->findOrFail($request->integer('camera_id'));
        }
        $watermarkPath = $user->custom_watermark_path;
        if ($request->hasFile('watermark')) {
            $watermarkPath = $request->file('watermark')->store('watermarks/photographers/'.$user->id, 'local');
            $user->forceFill(['custom_watermark_path' => $watermarkPath, 'photographer_watermark_locked' => $request->boolean('lock_watermark')])->save();
        }
        if (! $watermarkPath || (! $user->photographer_watermark_locked && ! $request->hasFile('watermark'))) {
            return back()->withErrors(['watermark' => 'Unggah watermark PNG/WEBP untuk batch ini, atau kunci watermark tersimpan.'])->withInput();
        }
        $batchBytes = collect($request->file('photos'))->sum(fn ($file) => $file->getSize());
        $usedBytes = (int) Photo::where('fotografer_id', $user->id)
            ->selectRaw('COALESCE(SUM(COALESCE(storage_bytes, file_size_mb * 1048576)), 0) AS total_bytes')
            ->value('total_bytes');
        if ($usedBytes + $batchBytes > $user->effectiveQuotaMb() * 1048576) {
            return back()->withErrors(['photos' => 'Storage tidak mencukupi.'])->withInput();
        }
        DB::transaction(function () use ($request, $validated, $user, $event, $watermarkPath, $processor): void {
            foreach ($request->file('photos') as $file) {
                $paths = $processor->process($file, Str::slug($event->nama_event), $watermarkPath);
                Photo::create([
                    'event_id' => $event->id, 'fotografer_id' => $user->id, 'camera_id' => $validated['camera_id'] ?? null,
                    'title' => ($validated['title'] ?? null) ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'file_asli' => $paths['original'], 'file_watermark' => $paths['preview'], 'purchased_path' => $paths['purchased'],
                    'personal_watermark_path' => $watermarkPath, 'media_type' => $paths['media_type'], 'storage_bytes' => $paths['bytes'],
                    'harga' => $validated['harga'], 'status' => 'active', 'published_at' => now(), 'taken_at' => $validated['taken_at'] ?? null,
                    'daypart' => $validated['daypart'] ?? null, 'category' => $validated['category'] ?? null,
                    'original_filename' => $file->getClientOriginalName(), 'file_size_mb' => round($paths['bytes'] / 1048576, 2),
                ]);
            }
            $user->forceFill(['storage_terpakai_mb' => (int) ceil(Photo::where('fotografer_id', $user->id)->sum('storage_bytes') / 1048576)])->save();
        });

        return back()->with('success', count($request->file('photos')).' file berhasil diunggah.');
    }

    public function update(Request $request, Photo $photo): RedirectResponse
    {
        abort_unless($photo->fotografer_id === $request->user()->id, 404);
        $data = $request->validate(['title' => ['nullable', 'string', 'max:255'], 'harga' => ['required', 'integer', 'min:0'], 'status' => ['required', 'in:active,inactive']]);
        $photo->update($data + ['published_at' => $data['status'] === 'active' ? ($photo->published_at ?? now()) : null]);

        return back()->with('success', 'Foto diperbarui.');
    }

    public function destroy(Photo $photo, Request $request): RedirectResponse
    {
        abort_unless($photo->fotografer_id === $request->user()->id, 404);
        Storage::disk('local')->delete(array_filter([$photo->file_asli, $photo->file_watermark, $photo->purchased_path]));
        $photo->delete();
        $request->user()->forceFill(['storage_terpakai_mb' => (int) ceil(Photo::where('fotografer_id', $request->user()->id)->sum('storage_bytes') / 1048576)])->save();

        return back()->with('success', 'Foto dihapus.');
    }

    public function watermark(Request $request): RedirectResponse
    {
        $request->validate(['watermark' => ['nullable', File::image()->types(['png', 'webp'])->max('5mb')], 'locked' => ['required', 'boolean']]);
        $user = $request->user();
        $path = $request->hasFile('watermark') ? $request->file('watermark')->store('watermarks/photographers/'.$user->id, 'local') : $user->custom_watermark_path;
        if ($request->boolean('locked') && ! $path) {
            return back()->withErrors(['watermark' => 'Unggah watermark sebelum menguncinya.']);
        }
        $user->forceFill(['custom_watermark_path' => $path, 'photographer_watermark_locked' => $request->boolean('locked')])->save();

        return back()->with('success', 'Pengaturan watermark diperbarui.');
    }

    private function resolveEvent(Request $request): Event
    {
        if ($request->filled('new_folder')) {
            return Event::firstOrCreate(['nama_event' => trim($request->string('new_folder')->toString()), 'fotografer_id' => $request->user()->id], ['tanggal_event' => now(), 'lokasi' => 'Online']);
        }

        return Event::where('fotografer_id', $request->user()->id)->findOrFail($request->integer('event_id'));
    }
}
