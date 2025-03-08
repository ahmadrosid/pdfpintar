<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Thread;
use App\Models\Message;
use Livewire\Component;

class ChatPage extends Component
{
    public Document $document;

    protected $messages = [];
    protected $threadId;

    public function clearMessages()
    {
        $this->authorize('view', $this->document);
        $thread = Thread::where('document_id', $this->document->id)->orderBy('created_at', 'desc')->first();
        if ($thread) {
            Message::where('thread_id', $thread->id)->delete();
            Thread::where('document_id', $this->document->id)->delete();
            $this->messages = [];
        }
    }

    public function mount()
    {
        if (!$this->document->is_public) {
            $this->authorize('view', $this->document);
        }

        $existingThread = Thread::where('document_id', $this->document->id)->orderBy('created_at', 'desc')->first();
        if (!$existingThread) {
            return;
        }

        $this->threadId = $existingThread->id;
        $this->messages = Message::where('thread_id', $existingThread->id)->orderBy('id', 'asc')->get();
    }

    public function render(): string
    {
        $document = e(json_encode($this->document));
        $messages = e(json_encode($this->messages));
        $csrf = csrf_token();
        return <<<HTML
        <div class="relative flex flex-col border border-neutral-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 h-full max-h-[93vh]">
            <div
                data-svelte="ChatPage.svelte"
                data-document="$document"
                data-messages="$messages"
                data-csrf="$csrf"
                data-thread-id="$this->threadId"
            ></div>
        </div>
        HTML;
    }
}
