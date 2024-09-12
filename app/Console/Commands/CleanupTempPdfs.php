<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempPdfs extends Command
{
    protected $signature = 'cleanup:temp-pdfs {--days=1 : Number of days to keep files}';
    protected $description = 'Clean up temporary PDF files older than specified days';

    public function handle()
    {
        $days = $this->option('days');
        $path = storage_path('app/public/pdf-temp');

        if (!File::isDirectory($path)) {
            $this->info("The directory {$path} does not exist. Nothing to clean up.");
            return;
        }

        $now = Carbon::now();
        $deletedCount = 0;

        foreach (File::files($path) as $file) {
            $fileCarbon = Carbon::createFromTimestamp(File::lastModified($file));
            if ($now->diffInDays($fileCarbon) >= $days) {
                File::delete($file);
                $deletedCount++;
            }
        }

        $this->info("Cleaned up {$deletedCount} PDF files older than {$days} days from {$path}");
    }
}