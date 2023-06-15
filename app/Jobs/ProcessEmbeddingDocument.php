<?php

namespace App\Jobs;

use App\Http\Repository\DocumentRepository;
use App\Models\Collection;
use App\Models\Document;
use App\Models\Embedding;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Mis3085\Tiktoken\Facades\Tiktoken;

class ProcessEmbeddingDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 2 * 60 * 60; // 2 hours
    protected DocumentRepository $documentRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Document $document,
    ) {
        $this->documentRepository = new DocumentRepository();
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return ['embedding', 'document:' . $this->document->id];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $home_dir = env('HOME_DIR');
        $collection = Collection::create([
            'name' => $this->document->path,
            'cmetadata' => json_encode([
                "total_token" => 0,
                "title" => $this->document->title
            ])
        ]);
        $pdf_path =  storage_path("app/" . $this->document->path);
        $texts = php_pdf_read_all($pdf_path);
        $total_token_embed = 0;
        $page_number = 0;
        foreach ($texts as $text) {
            $page_number++;
            $total_token = Tiktoken::count($text);
            $total_token_embed += $total_token;
            $vectors = $this->documentRepository->getQueryEmbedding($text);
            Embedding::create([
                "collection_id" => $collection->uuid,
                "embedding" => json_encode($vectors),
                "document" => $text,
                "cmetadata" => json_encode([
                    "total_token" => $total_token,
                    "page" => $page_number,
                    "path" => $this->document->path,
                    "title" => $this->document->title
                ])
            ]);
            $this->document->update([
                'status' => "Embedding page {$page_number}"
            ]);
        }

        $collection->update([
            'cmetadata' => json_encode([
                "total_token" => $total_token_embed,
                "title" => $this->document->title
            ])
        ]);

        $this->document->update([
            'status' => "complete"
        ]);
        echo "total_token_embed: $total_token_embed\n";
    }
}
