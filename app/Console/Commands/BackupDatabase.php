<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup SQLite database to S3';

    public function handle()
    {
        $localPath = database_path('database.sqlite');
        $timestamp = Carbon::now()->format('Y-m-d-H-i-s');
        $s3Path = "backups/database-{$timestamp}.sqlite";

        try {
            $contents = file_get_contents($localPath);
            Storage::disk('s3')->put($s3Path, $contents);
            $this->info("Database backed up successfully to: {$s3Path}");
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
        }
    }
}
