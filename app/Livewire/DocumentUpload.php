<?php

namespace App\Livewire;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use OpenAI\Laravel\Facades\OpenAI;
use Livewire\Attributes\On;

class DocumentUpload extends Component
{
    use WithFileUploads;

    public $document;

    #[On('upload:finished')]
    public function handleUploadFinished()
    {
        if (!$this->document) return;

        try {
            // Upload to OpenAI first to get file ID
            $response = OpenAI::files()->upload([
                'file' => fopen($this->document->getPathname(), 'r'),
                'purpose' => 'assistants',
            ]);

            // Store file in S3 with private visibility
            $filename = $this->document->getClientOriginalName();
            $path = $this->document->storeAs(
                'documents',
                time() . '_' . $filename,
                ['disk' => 's3', 'visibility' => 'private']
            );

            $document = Document::create([
                'file_id' => $response->id,
                'file_name' => $filename,
                'file_size' => $this->document->getSize(),
                'file_path' => $path,
                'user_id' => Auth::id(),
            ]);

            $this->reset('document');
            $this->dispatch('document-uploaded');
            return redirect()->route('documents.show', $document);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }

    public function updatedDocument()
    {
        $this->validate([
            'document' => 'file|mimes:pdf|max:10240',
        ]);
    }

    public function render()
    {
        return view('livewire.document-upload');
    }
}
