<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
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
            ->get()
            ->map(function ($document) {
                return [
                    'id' => $document->id,
                    'file_name' => $document->file_name,
                    'created_at' => $document->created_at->diffForHumans(),
                ];
            });
    }

    public function updatedSearch()
    {
        $this->loadDocuments();
    }

    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        if ($document->user_id == Auth::user()->id) {
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

    public function render(): string
    {
        $documents = e(json_encode($this->documents));
        $labels = e(json_encode([
            'search_document' => __('Search documents'),
            'click_to_chat' => __('Click to chat with the document'),
            'no_documents' => __('No documents found.'),
            'upload_pdf' => __('Upload PDF'),
        ]));
        $csrf = csrf_token();
        return <<<HTML
        <div class="py-12">
            <div
                data-svelte="DocumentList.svelte"
                data-documents="$documents"
                data-labels="$labels"
                data-csrf="$csrf"
            ></div>
        </div>
        HTML;
    }
}
