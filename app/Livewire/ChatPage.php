<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Thread;
use App\Models\Message;
use Livewire\Component;
use App\Support\Svelte;

class ChatPage extends Component
{
    public Document $document;

    protected $messages = [];
    public $thread = null;

    public function clearMessages()
    {
        $this->authorize('view', $this->document);
        if ($this->thread) {
            Message::where('thread_id', $this->thread->id)->delete();
            Thread::where('document_id', $this->document->id)->delete();
            $this->messages = [];
        }
    }

    public function mount()
    {
        if (!$this->document->is_public) {
            $this->authorize('view', $this->document);
        }

        $this->thread = Thread::where('document_id', $this->document->id)->orderBy('created_at', 'desc')->first();
        if (!$this->thread) {
            return;
        }

        $this->messages = Message::where('thread_id', $this->thread->id)->orderBy('id', 'asc')->get();
    }

    public function render(): string
    {
        return Svelte::render('ChatPage.svelte', [
            'document' => $this->document,
            'messages' => $this->messages,
            'thread' => $this->thread,
            'labels' => [
                'delete' => __('Delete'),
                'download_as_pdf' => __("Download As PDF"),
                'download_as_excel' => __("Download As Excel"),
                'download_as_word' => __("Download As Word"),
            ]
        ], [
            'class' => 'relative flex flex-col border border-neutral-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 h-full max-h-[93vh]'
        ]);
    }
}
