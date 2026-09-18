<?php

namespace App\Console\Commands;

use App\Models\Photo;
use App\PhotoProcessor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('photos:reprocess-watermarks {--photographer= : Only process one photographer ID}')]
#[Description('Regenerate public and purchased photo variants from stored originals')]
class ReprocessPhotoWatermarks extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(PhotoProcessor $processor): int
    {
        $processed = 0;
        $skipped = 0;
        $query = Photo::query()->orderBy('id');

        if ($photographerId = $this->option('photographer')) {
            $query->where('fotografer_id', $photographerId);
        }

        $query->eachById(function (Photo $photo) use ($processor, &$processed, &$skipped): void {
            try {
                $processor->reprocess($photo);
                $processed++;
            } catch (\RuntimeException $exception) {
                $skipped++;
                $this->warn("Photo {$photo->id} skipped: {$exception->getMessage()}");
            }
        });

        $this->info("Processed {$processed} photo(s); skipped {$skipped}.");

        return self::SUCCESS;
    }
}
