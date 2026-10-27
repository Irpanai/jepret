<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\Event;
use App\Models\Photo;
use App\Models\User;
use App\PhotoProcessor;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PhotoController extends Controller
{
    private const MAX_IMAGE_DIMENSION = 30000;

    private const MAX_TOTAL_PIXELS = 100000000;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'event' => ['nullable', 'integer'],
            'camera' => ['nullable', 'integer'],
            'category' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:newest,oldest,title,price_low,price_high'],
        ]);
        $events = Event::where('fotografer_id', $request->user()->id)->withCount('photos')->latest()->get();
        $cameras = Camera::where('fotografer_id', $request->user()->id)->orderBy('name')->get();
        $categories = Photo::query()
            ->where('fotografer_id', $request->user()->id)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        $search = trim($filters['q'] ?? '');
        $photos = Photo::query()
            ->with(['event', 'camera'])
            ->where('fotografer_id', $request->user()->id)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('original_filename', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('ai_tags', 'like', "%{$search}%")
                        ->orWhereHas('event', fn ($eventQuery) => $eventQuery->where('nama_event', 'like', "%{$search}%"))
                        ->orWhereHas('camera', fn ($cameraQuery) => $cameraQuery->where('name', 'like', "%{$search}%")->orWhere('brand_model', 'like', "%{$search}%"));
                });
            })
            ->when(isset($filters['event']), fn ($query) => $query->where('event_id', $filters['event']))
            ->when(isset($filters['camera']), fn ($query) => $query->where('camera_id', $filters['camera']))
            ->when(isset($filters['category']), fn ($query) => $query->where('category', $filters['category']))
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(
                ($filters['sort'] ?? 'newest') === 'oldest',
                fn ($query) => $query->oldest(),
                fn ($query) => match ($filters['sort'] ?? 'newest') {
                    'title' => $query->orderBy('title'),
                    'price_low' => $query->orderBy('harga'),
                    'price_high' => $query->orderByDesc('harga'),
                    default => $query->latest(),
                },
            )
            ->paginate(18)
            ->withQueryString();

        $user = $request->user()->load(['package', 'subscription']);
        $storageUsage = [
            'used_bytes' => (int) Photo::where('fotografer_id', $user->id)->sum('storage_bytes'),
            'quota_bytes' => $user->effectiveQuotaBytes(),
        ];
        $acceptedPhotoExtensions = PhotoProcessor::supportedExtensions();

        return view('fotografer.photos.index', compact('events', 'cameras', 'categories', 'photos', 'storageUsage', 'acceptedPhotoExtensions'));
    }

    public function create(Request $request): View
    {
        $events = Event::where('fotografer_id', $request->user()->id)->latest()->get();
        $cameras = Camera::where('fotografer_id', $request->user()->id)->orderBy('name')->get();

        return view('fotografer.photos.create', compact('events', 'cameras'));
    }

    public function store(Request $request, PhotoProcessor $processor): RedirectResponse|JsonResponse
    {
        abort_unless($request->user()->is_verified, 403, 'Akun photographer harus disetujui sebelum mengunggah foto.');
        if ($request->filled('taken_at')) {
            $request->merge(['taken_at' => $this->toUtc($request->string('taken_at')->toString())]);
        }
        $supportedExtensions = PhotoProcessor::supportedExtensions();
        $validated = $request->validate([
            'event_id' => ['nullable', 'required_without:new_folder', 'integer'],
            'new_folder' => ['nullable', 'required_without:event_id', 'string', 'max:255'],
            'camera_id' => ['nullable', 'integer'], 'title' => ['nullable', 'string', 'max:255'],
            'taken_at' => ['nullable', 'date'], 'daypart' => ['nullable', 'in:morning,afternoon,evening,night'],
            'category' => ['nullable', 'string', 'max:100'], 'harga' => ['required', 'integer', 'min:0'],
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => [
                'required',
                File::types($supportedExtensions)->max('100mb'),
                'extensions:'.implode(',', $supportedExtensions),
                function (string $attribute, mixed $value, Closure $fail) use ($processor): void {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }

                    try {
                        $image = $processor->inspect($value);
                    } catch (Throwable) {
                        $fail('File gambar tidak valid atau tidak dapat dibaca.');

                        return;
                    }

                    if ($image['width'] > self::MAX_IMAGE_DIMENSION || $image['height'] > self::MAX_IMAGE_DIMENSION) {
                        $fail('Dimensi gambar maksimal adalah 30.000 piksel pada setiap sisi.');
                    }

                    if ($image['pixels'] > self::MAX_TOTAL_PIXELS) {
                        $fail('Total piksel gambar, termasuk seluruh frame GIF, maksimal 100 megapiksel.');
                    }
                },
            ],
            'upload_token' => ['nullable', 'uuid'],
            'sell_without_watermark' => ['nullable', 'boolean'],
            'watermark' => ['nullable', File::image()->types(['png', 'webp'])->max('5mb')], 'lock_watermark' => ['nullable', 'boolean'],
            'watermark_settings' => ['nullable', 'array'],
            'watermark_settings.*.x' => ['required', 'numeric', 'between:0,100'],
            'watermark_settings.*.y' => ['required', 'numeric', 'between:0,100'],
            'watermark_settings.*.scale' => ['required', 'numeric', 'between:5,80'],
            'watermark_settings.*.opacity' => ['required', 'numeric', 'between:5,100'],
            'photo_titles' => ['nullable', 'array'],
            'photo_titles.*' => ['nullable', 'string', 'max:255'],
        ], [
            'event_id.required_without' => 'Pilih event yang sudah ada atau masukkan nama event baru.',
            'new_folder.required_without' => 'Pilih event yang sudah ada atau masukkan nama event baru.',
            'photos.required' => 'Pilih setidaknya satu gambar untuk diunggah.',
            'photos.*.extensions' => 'Ekstensi gambar tidak didukung oleh server.',
            'photos.*.mimes' => 'Jenis MIME gambar tidak didukung oleh server.',
            'photos.*.max' => 'Ukuran setiap gambar maksimal 100 MB.',
        ]);
        $user = $request->user()->load(['package', 'subscription']);
        if (isset($validated['upload_token'])) {
            $existing = Photo::where('fotografer_id', $user->id)->where('upload_token', $validated['upload_token'])->first();
            if ($existing) {
                return $this->uploadResponse($request, $existing, true);
            }
        }
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
            throw ValidationException::withMessages(['watermark' => 'Unggah watermark PNG/WEBP untuk batch ini, atau kunci watermark tersimpan.']);
        }
        $createdPaths = [];
        $createdPhoto = null;
        try {
            DB::transaction(function () use ($request, $validated, $user, $event, $watermarkPath, $processor, &$createdPaths, &$createdPhoto): void {
                $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail()->load(['package', 'subscription']);
                $usedBytes = (int) Photo::where('fotografer_id', $user->id)->sum('storage_bytes');
                $quotaBytes = $lockedUser->effectiveQuotaBytes();
                $incomingBytes = (int) collect($request->file('photos'))->sum(fn ($file) => $file->getSize());
                if ($usedBytes + $incomingBytes > $quotaBytes) {
                    throw ValidationException::withMessages(['photos' => 'Storage tidak mencukupi.']);
                }
                foreach ($request->file('photos') as $index => $file) {
                    $watermarkSettings = $validated['watermark_settings'][$index] ?? [
                        'x' => 50,
                        'y' => 85,
                        'scale' => 30,
                        'opacity' => 100,
                    ];
                    try {
                        $paths = $processor->process($file, Str::slug($event->nama_event), $watermarkPath, $watermarkSettings);
                    } catch (Throwable $exception) {
                        report($exception);
                        throw ValidationException::withMessages(['photos' => 'Gambar gagal diproses. Pastikan file tidak rusak dan formatnya didukung server.']);
                    }
                    $createdPaths[] = $paths['original'];
                    $createdPaths[] = $paths['preview'];
                    $createdPaths[] = $paths['purchased'];
                    if ($usedBytes + $paths['bytes'] > $quotaBytes) {
                        throw ValidationException::withMessages(['photos' => 'Storage tidak mencukupi.']);
                    }
                    $usedBytes += $paths['bytes'];
                    $selectedCamera = ! empty($validated['camera_id'])
                        ? Camera::where('fotografer_id', $user->id)->find($validated['camera_id'])
                        : null;
                    $createdPhoto = Photo::create([
                        'upload_token' => count($request->file('photos')) === 1 ? ($validated['upload_token'] ?? null) : null,
                        'event_id' => $event->id,
                        'fotografer_id' => $user->id,
                        'camera_id' => $selectedCamera?->id,
                        'kamera_body' => $selectedCamera?->brand_model ?: $selectedCamera?->name,
                        'lensa' => $selectedCamera?->lens,
                        'title' => trim((string) ($validated['photo_titles'][$index] ?? $validated['title'] ?? '')) ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                        'file_asli' => $paths['original'], 'file_watermark' => $paths['preview'], 'purchased_path' => $paths['purchased'],
                        'original_sha256' => $paths['original_sha256'],
                        'sell_without_watermark' => $request->has('sell_without_watermark') ? $request->boolean('sell_without_watermark') : true,
                        'personal_watermark_path' => $watermarkPath, 'media_type' => $paths['media_type'], 'storage_bytes' => $paths['bytes'],
                        'original_bytes' => $paths['original_bytes'], 'preview_bytes' => $paths['preview_bytes'], 'purchased_bytes' => $paths['purchased_bytes'],
                        'watermark_settings' => $watermarkSettings,
                        'harga' => $validated['harga'], 'status' => 'active', 'published_at' => now(), 'taken_at' => $validated['taken_at'] ?? null,
                        'daypart' => $validated['daypart'] ?? null, 'category' => $validated['category'] ?? null,
                        'original_filename' => $file->getClientOriginalName(), 'file_size_mb' => round($paths['bytes'] / 1048576, 2),
                    ]);
                }
                $lockedUser->forceFill(['storage_terpakai_mb' => (int) ceil($usedBytes / 1048576)])->save();
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($createdPaths);
            throw $exception;
        }

        if ($request->expectsJson() && $createdPhoto) {
            return $this->uploadResponse($request, $createdPhoto);
        }

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
        if ($photo->transactions()->exists()) {
            $photo->update(['status' => 'inactive', 'published_at' => null]);

            return back()->with('success', 'Foto yang pernah terjual diarsipkan agar hak unduh Buyer tetap tersedia.');
        }
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

    public function watermarkPreview(Request $request): StreamedResponse
    {
        $path = $request->user()->custom_watermark_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    private function resolveEvent(Request $request): Event
    {
        $newFolder = trim($request->string('new_folder')->toString());
        if ($newFolder !== '') {
            $eventLocation = trim($request->string('event_location')->toString());
            if ($eventLocation === '') {
                $eventLocation = $request->user()->location ?: 'Belum dicatat';
            }

            return Event::firstOrCreate(
                ['nama_event' => $newFolder, 'fotografer_id' => $request->user()->id],
                ['tanggal_event' => now(), 'lokasi' => $eventLocation]
            );
        }

        $eventId = $request->integer('event_id');
        if ($eventId <= 0) {
            throw ValidationException::withMessages(['event_id' => 'Pilih event yang valid atau buat event baru.']);
        }

        $event = Event::where('fotografer_id', $request->user()->id)->find($eventId);
        if (! $event) {
            throw ValidationException::withMessages(['event_id' => 'Event yang dipilih tidak ditemukan.']);
        }

        return $event;
    }

    private function toUtc(string $value): string
    {
        try {
            return Carbon::createFromFormat('Y-m-d\TH:i', $value, config('app.display_timezone'))->utc()->format('Y-m-d H:i:s');
        } catch (Throwable) {
            return $value;
        }
    }

    private function uploadResponse(Request $request, Photo $photo, bool $duplicate = false): JsonResponse
    {
        $usedBytes = (int) Photo::where('fotografer_id', $request->user()->id)->sum('storage_bytes');

        return response()->json([
            'photo_id' => $photo->id,
            'duplicate' => $duplicate,
            'used_bytes' => $usedBytes,
            'stored_bytes' => (int) $photo->storage_bytes,
        ]);
    }
}
