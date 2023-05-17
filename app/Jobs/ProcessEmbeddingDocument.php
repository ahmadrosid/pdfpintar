<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class ProcessEmbeddingDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Document $document,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $home_dir = env('HOME_DIR');
        $result = Process::run("python3 {$home_dir}/ingest.py " . "{$home_dir}/storage/app/" . $this->document->path);
        dump("python3 {$home_dir}/ingest.py " . "{$home_dir}/storage/app/" . $this->document->path);
        dump($result->output());
        $this->document->update(["job_id" => null]);
    }
}
