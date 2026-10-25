<?php

namespace App\Console\Commands;

use App\Models\Photo;
use App\Models\Transaction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('photos:backfill-checksums')]
#[Description('Calculate missing SHA-256 checksums for original photos and purchase snapshots')]
class BackfillPhotoChecksums extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = Storage::disk('local');
        $updatedPhotos = 0;
        $updatedTransactions = 0;
        $missingFiles = 0;

        Photo::query()->whereNull('original_sha256')->eachById(function (Photo $photo) use ($disk, &$updatedPhotos, &$missingFiles): void {
            if (! $photo->file_asli || ! $disk->exists($photo->file_asli)) {
                $missingFiles++;

                return;
            }

            $photo->forceFill(['original_sha256' => hash_file('sha256', $disk->path($photo->file_asli))])->save();
            $updatedPhotos++;
        });

        Transaction::query()
            ->where('download_without_watermark', true)
            ->whereNull('download_sha256')
            ->whereNotNull('download_path')
            ->eachById(function (Transaction $transaction) use ($disk, &$updatedTransactions, &$missingFiles): void {
                if (! $disk->exists($transaction->download_path)) {
                    $missingFiles++;

                    return;
                }

                $transaction->forceFill(['download_sha256' => hash_file('sha256', $disk->path($transaction->download_path))])->save();
                $updatedTransactions++;
            });

        $this->info("Updated {$updatedPhotos} photo checksum(s) and {$updatedTransactions} transaction checksum(s); {$missingFiles} file(s) missing.");

        return $missingFiles === 0 ? self::SUCCESS : self::FAILURE;
    }
}
