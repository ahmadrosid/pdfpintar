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
        $this->threads = $document->threads;
        $this->currentThread = $this->threads->first();
    }

    public function render()
    {
        return view('livewire.public-chat-interface', [
            'messages' => $this->currentThread?->messages ?? collect(),
        ]);
    }

    public function selectThread(Thread $thread)
    {
        $this->currentThread = $thread;
    }
}
