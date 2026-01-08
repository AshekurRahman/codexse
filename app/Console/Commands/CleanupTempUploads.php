<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempUploads extends Command
{
    protected $signature = 'uploads:cleanup-temp {--hours=24 : Delete files older than this many hours}';

    protected $description = 'Clean up orphaned temporary upload files';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = Carbon::now()->subHours($hours);

        $this->info("Cleaning up temp uploads older than {$hours} hours...");

        $disk = Storage::disk('public');
        $directory = 'product-requests/temp';

        if (!$disk->exists($directory)) {
            $this->info('Temp directory does not exist. Nothing to clean up.');
            return Command::SUCCESS;
        }

        $files = $disk->files($directory);
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

            if ($lastModified->lt($cutoff)) {
                $disk->delete($file);
                $deletedCount++;
                $this->line("Deleted: {$file}");
            }
        }

        $this->info("Cleaned up {$deletedCount} orphaned temp files.");

        return Command::SUCCESS;
    }
}
