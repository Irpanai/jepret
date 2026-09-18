<?php

namespace App;

use App\Models\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PhotoProcessor
{
    private const SYSTEM_WATERMARK_SCALE = 100;

    private const PHOTOGRAPHER_WATERMARK_SCALE = 30;

    /** @return array{original:string,preview:string,purchased:string,bytes:int,media_type:string} */
    public function process(UploadedFile $file, string $eventSlug, string $personalWatermarkPath): array
    {
        $uuid = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension());
        $mediaType = $extension === 'mov' ? 'video' : 'image';
        $original = $file->storeAs('photos/original/'.$eventSlug, $uuid.'.'.$extension, 'local');
        $preview = 'photos/preview/'.$eventSlug.'/'.$uuid.'.'.($mediaType === 'video' ? 'mp4' : 'jpg');
        $purchased = 'photos/purchased/'.$eventSlug.'/'.$uuid.'.'.($mediaType === 'video' ? 'mp4' : 'jpg');

        try {
            if ($mediaType === 'video') {
                $this->processVideo(Storage::disk('local')->path($original), $preview, $purchased, $personalWatermarkPath);
            } else {
                $this->processImage(Storage::disk('local')->path($original), $preview, $purchased, $personalWatermarkPath);
            }
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete([$original, $preview, $purchased]);

            throw $exception;
        }

        return ['original' => $original, 'preview' => $preview, 'purchased' => $purchased, 'bytes' => $file->getSize(), 'media_type' => $mediaType];
    }

    private function processImage(string $source, string $preview, string $purchased, string $personalWatermarkPath): void
    {
        $manager = new ImageManager(new Driver);
        foreach ([[$preview, public_path('images/watermarksistemjepret.png'), self::SYSTEM_WATERMARK_SCALE, 'center'], [$purchased, Storage::disk('local')->path($personalWatermarkPath), self::PHOTOGRAPHER_WATERMARK_SCALE, 'bottom-center']] as [$path, $markPath, $scale, $position]) {
            $image = $manager->decodePath($source);
            if ($path === $preview) {
                $image->blur(2);
            }
            $mark = $manager->decodePath($markPath);
            $mark->scale(width: max(80, (int) ($image->width() * $scale / 100)));
            $image->insert($mark, 0, $position === 'bottom-center' ? 20 : 0, $position, 1);
            Storage::disk('local')->makeDirectory(dirname($path));
            $image->save(Storage::disk('local')->path($path), quality: 95);
        }
    }

    private function processVideo(string $source, string $preview, string $purchased, string $personalWatermarkPath): void
    {
        foreach ([[$preview, public_path('images/watermarksistemjepret.png'), 1.0, '(W-w)/2:(H-h)/2', true], [$purchased, Storage::disk('local')->path($personalWatermarkPath), 0.30, '(W-w)/2:H-h-20', false]] as [$path, $markPath, $scale, $overlay, $blur]) {
            Storage::disk('local')->makeDirectory(dirname($path));
            $baseFilter = $blur ? '[0:v]gblur=sigma=1.2[content];' : '';
            $baseInput = $blur ? '[content]' : '[0:v]';
            Process::timeout(300)->run(['ffmpeg', '-y', '-i', $source, '-i', $markPath, '-filter_complex', $baseFilter.'[1:v]'.$baseInput.'scale2ref=w=main_w*'.$scale.':h=ow/mdar[wm][base];[base][wm]overlay='.$overlay, '-c:v', 'libx264', '-crf', '20', '-preset', 'fast', '-c:a', 'aac', Storage::disk('local')->path($path)])->throw();
        }
    }

    public function reprocess(Photo $photo): void
    {
        if (! $photo->file_asli || ! $photo->file_watermark || ! $photo->purchased_path || ! $photo->personal_watermark_path) {
            throw new \RuntimeException('Photo paths are incomplete.');
        }

        $disk = Storage::disk('local');
        if (! $disk->exists($photo->file_asli) || ! $disk->exists($photo->personal_watermark_path)) {
            throw new \RuntimeException('Original photo or photographer watermark is missing.');
        }

        if ($photo->media_type === 'video') {
            $this->processVideo($disk->path($photo->file_asli), $photo->file_watermark, $photo->purchased_path, $photo->personal_watermark_path);

            return;
        }

        $this->processImage($disk->path($photo->file_asli), $photo->file_watermark, $photo->purchased_path, $photo->personal_watermark_path);
    }
}
