<?php

namespace App\Livewire;

use App\Lib\ExcelProcessor;
use App\Lib\PdfProcessor;
use App\Lib\WordProcessor;
use App\Models\Document;
use App\Models\Thread;
use App\Models\Message;
use Livewire\Attributes\Computed;
use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Livewire\Attributes\On;
use Throwable;
use Illuminate\Support\Str;

class ChatPage extends Component
{
    public Document $document;

    public function render(): string
    {
        $document = e(json_encode($this->document));
        return <<<HTML
        <div class="relative flex flex-col border border-neutral-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 h-full max-h-[93vh]">
            <div
                data-svelte="ChatPage.svelte"
                data-document="$document"
            ></div>
        </div>
        HTML;
    }
}
