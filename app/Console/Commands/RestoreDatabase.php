<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RestoreDatabase extends Command
{
    protected $signature = 'db:restore';
    protected $description = 'Restore latest SQLite database backup from S3';

    public function handle()
    {
        try {
            $files = Storage::disk('s3')->files('backups');
            $latestBackup = collect($files)
                ->filter(function ($file) {
                    return str_starts_with($file, 'backups/database-');
                })
                ->sort()
                ->last();

            if (!$latestBackup) {
                $this->error('No backup found');
                return;
            }

            $contents = Storage::disk('s3')->get($latestBackup);
            file_put_contents(database_path('database.sqlite'), $contents);
            $this->info("Database restored successfully from: {$latestBackup}");
        } catch (\Exception $e) {
            $this->error("Restore failed: " . $e->getMessage());
        }
    }
}
