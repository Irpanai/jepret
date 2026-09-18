<?php

namespace App;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProfilePhotoProcessor
{
    public function process(UploadedFile $file, int $zoom, int $positionX, int $positionY): string
    {
        $size = 800;
        $zoomedSize = (int) round($size * $zoom / 100);
        $manager = new ImageManager(new Driver);
        $image = $manager->decodePath($file->getRealPath())->cover($size, $size);

        if ($zoomedSize > $size) {
            $image->resize($zoomedSize, $zoomedSize);
            $maxOffset = $zoomedSize - $size;
            $image->crop(
                $size,
                $size,
                (int) round($maxOffset * $positionX / 100),
                (int) round($maxOffset * $positionY / 100),
            );
        }

        $path = 'profiles/'.Str::uuid().'.jpg';
        Storage::disk('public')->makeDirectory('profiles');
        $image->save(Storage::disk('public')->path($path), quality: 92);

        return $path;
    }
}
