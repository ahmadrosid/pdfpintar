<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use App\Support\Svelte;

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

    public function searchDocument($search)
    {
        $this->search = $search;
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

    public function render(): string
    {
        return Svelte::render('DocumentList.svelte', [
            'documents' => $this->documents,
            'labels' => [
                'search_document' => __('Search documents'),
                'click_to_chat' => __('Click to chat with the document'),
                'no_documents' => __('No documents found.'),
                'upload_pdf' => __('Upload PDF'),
                'delete_document' => __('Delete Document'),
                'delete_document_description' => __('Are you sure you want to delete this document?'),
                'upload_document' => __('Upload Document'),
                'upload_document_description' => __('Upload your PDF document here'),
                'click_to_upload' => __('Click to upload'),
                'or_drag_and_drop' => __('or drag and drop'),
                'cancel' => __('Cancel'),
                'delete' => __('Delete'),
                'uploading' => __('Uploading'),
                'upload' => __('Upload'),
            ]
        ], [
            'class' => 'py-12'
        ]);
    }
}
