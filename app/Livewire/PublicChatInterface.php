<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Thread;
use Livewire\Component;

class PublicChatInterface extends Component
{
    public Document $document;
    public $threads;
    public $currentThread;

    public function mount(Document $document)
    {
        $this->document = $document;
        // Get all threads for the shared document
        $this->threads = $document->threads()->orderBy('created_at', 'desc')->get();
        $this->currentThread = $this->threads->first();
    }

    public function render()
    {
        return view('livewire.public-chat-interface', [
            'messages' => $this->currentThread?->messages()->orderBy('id', 'asc')->get() ?? collect(),
        ]);
    }

    public function selectThread(Thread $thread)
    {
        $this->currentThread = $thread;
    }
}
