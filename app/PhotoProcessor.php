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
    public function process(UploadedFile $file, string $eventSlug, string $personalWatermarkPath, array $watermarkSettings = []): array
    {
        $uuid = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension());
        $mediaType = $extension === 'mov' ? 'video' : 'image';
        $original = $file->storeAs('photos/original/'.$eventSlug, $uuid.'.'.$extension, 'local');
        $preview = 'photos/preview/'.$eventSlug.'/'.$uuid.'.'.($mediaType === 'video' ? 'mp4' : 'jpg');
        $purchased = 'photos/purchased/'.$eventSlug.'/'.$uuid.'.'.($mediaType === 'video' ? 'mp4' : 'jpg');

        try {
            if ($mediaType === 'video') {
                $this->processVideo(Storage::disk('local')->path($original), $preview, $purchased, $personalWatermarkPath, $watermarkSettings);
            } else {
                $this->processImage(Storage::disk('local')->path($original), $preview, $purchased, $personalWatermarkPath, $watermarkSettings);
            }
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete([$original, $preview, $purchased]);

            throw $exception;
        }

        return ['original' => $original, 'preview' => $preview, 'purchased' => $purchased, 'bytes' => $file->getSize(), 'media_type' => $mediaType];
    }

    private function processImage(string $source, string $preview, string $purchased, string $personalWatermarkPath, array $watermarkSettings = []): void
    {
        $manager = new ImageManager(new Driver);
        $previewImage = $manager->decodePath($source)->blur(2);
        $systemMark = $manager->decodePath(public_path('images/watermarksistemjepret.png'));
        $systemMark->scale(width: max(80, (int) ($previewImage->width() * self::SYSTEM_WATERMARK_SCALE / 100)));
        $previewImage->insert($systemMark, 0, 0, 'center');
        Storage::disk('local')->makeDirectory(dirname($preview));
        $previewImage->save(Storage::disk('local')->path($preview), quality: 95);

        $settings = $this->normalizeWatermarkSettings($watermarkSettings);
        $purchasedImage = $manager->decodePath($source);
        $personalMark = $manager->decodePath(Storage::disk('local')->path($personalWatermarkPath));
        $personalMark->scale(width: max(1, (int) ($purchasedImage->width() * $settings['scale'] / 100)));
        $x = (int) round(($purchasedImage->width() * $settings['x'] / 100) - ($personalMark->width() / 2));
        $y = (int) round(($purchasedImage->height() * $settings['y'] / 100) - ($personalMark->height() / 2));
        $purchasedImage->insert($personalMark, $x, $y, transparency: $settings['opacity'] / 100);
        Storage::disk('local')->makeDirectory(dirname($purchased));
        $purchasedImage->save(Storage::disk('local')->path($purchased), quality: 95);
    }

    private function processVideo(string $source, string $preview, string $purchased, string $personalWatermarkPath, array $watermarkSettings = []): void
    {
        $settings = $this->normalizeWatermarkSettings($watermarkSettings);
        $x = $settings['x'] / 100;
        $y = $settings['y'] / 100;
        $opacity = $settings['opacity'] / 100;
        $personalOverlay = "W*{$x}-w/2:H*{$y}-h/2";

        foreach ([[$preview, public_path('images/watermarksistemjepret.png'), 1.0, '(W-w)/2:(H-h)/2', true, 1.0], [$purchased, Storage::disk('local')->path($personalWatermarkPath), $settings['scale'] / 100, $personalOverlay, false, $opacity]] as [$path, $markPath, $scale, $overlay, $blur, $markOpacity]) {
            Storage::disk('local')->makeDirectory(dirname($path));
            $baseFilter = $blur ? '[0:v]gblur=sigma=1.2[content];' : '';
            $baseInput = $blur ? '[content]' : '[0:v]';
            $watermarkFilter = $markOpacity < 1 ? '[1:v]format=rgba,colorchannelmixer=aa='.$markOpacity.'[mark];[mark]' : '[1:v]';
            Process::timeout(300)->run(['ffmpeg', '-y', '-i', $source, '-i', $markPath, '-filter_complex', $baseFilter.$watermarkFilter.$baseInput.'scale2ref=w=main_w*'.$scale.':h=ow/mdar[wm][base];[base][wm]overlay='.$overlay, '-c:v', 'libx264', '-crf', '20', '-preset', 'fast', '-c:a', 'aac', Storage::disk('local')->path($path)])->throw();
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
            $this->processVideo($disk->path($photo->file_asli), $photo->file_watermark, $photo->purchased_path, $photo->personal_watermark_path, $photo->watermark_settings ?? []);

            return;
        }

        $this->processImage($disk->path($photo->file_asli), $photo->file_watermark, $photo->purchased_path, $photo->personal_watermark_path, $photo->watermark_settings ?? []);
    }

    /** @return array{x:float, y:float, scale:float, opacity:float} */
    private function normalizeWatermarkSettings(array $settings): array
    {
        return [
            'x' => (float) ($settings['x'] ?? 50),
            'y' => (float) ($settings['y'] ?? 85),
            'scale' => (float) ($settings['scale'] ?? self::PHOTOGRAPHER_WATERMARK_SCALE),
            'opacity' => (float) ($settings['opacity'] ?? 100),
        ];
    }
}
