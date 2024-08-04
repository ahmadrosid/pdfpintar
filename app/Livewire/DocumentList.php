<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Livewire\Attributes\On;

class DocumentList extends Component
{
    public $documents;
    public $search = '';

    public function boot()
    {
        $this->loadDocuments();
    }

    public function loadDocuments()
    {
        $this->documents = Document::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('file_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updatedSearch()
    {
        $this->loadDocuments();
    }

    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        if ($document->user_id == auth()->id()) {
            $document->delete();
        }
        $this->loadDocuments();
    }

    #[On('close-modal')] 
    public function reloadDocuments($event)
    {
        if ($event == 'document-list-modal') {
            $this->loadDocuments();
        }
    }

    public function render()
    {
        return view('livewire.document-list');
    }
}