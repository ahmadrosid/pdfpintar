<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentChatbot extends Component
{
    public Document $document;

    public function render()
    {
        return view('livewire.document-chatbot');
    }
}
