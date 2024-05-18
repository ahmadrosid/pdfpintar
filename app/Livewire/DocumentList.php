<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Livewire\Attributes\On;

class DocumentList extends Component
{
    public $documents;

    public function boot()
    {
        $this->documents = Document::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
    }

    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        if ($document->user_id == auth()->id()) {
            $document->delete();
        }
        $this->documents = Document::where('user_id', auth()->id())->get();
    }

    #[On('close-modal')] 
    public function reloadDocuments($event)
    {
        if ($event == 'document-list-modal') {
            $this->documents = Document::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        }
    }

    public function render()
    {
        return view('livewire.document-list');
    }
}
