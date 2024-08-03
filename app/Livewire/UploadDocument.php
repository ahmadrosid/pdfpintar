<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;
use Spatie\LivewireFilepond\WithFilePond;

class UploadDocument extends Component
{
    use WithFilePond;

    public $file;
    public $show_modal = false;
    public $isUploading = false;
    public $uploadedDocument = null;

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|max:12048',
        ]);

        $this->isUploading = true;

        // Optimistically update the UI
        $this->uploadedDocument = [
            'file_name' => $this->file->getClientOriginalName(),
            'file_size' => $this->file->getSize(),
        ];

        // Close the modal optimistically
        $this->show_modal = false;
        $this->dispatch('close-modal', 'upload-document-modal');

        // Perform the actual upload
        $this->performUpload();
    }

    private function performUpload()
    {
        $file = $this->file;
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        $filePath = $file->store('documents', 'public');
        
        // OpenAI file upload
        $response = OpenAI::files()->upload([
            'file' => fopen(storage_path("app/public/" . $filePath), 'r'),
            'purpose' => 'assistants',
        ]);

        $fileId = $response->id;

        // Create document in database
        $document = Document::create([
            'file_path' => $filePath,
            'file_id' => $fileId,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'user_id' => Auth::id(),
        ]);

        $this->reset('file');
        $this->isUploading = false;
        
        // Update the uploadedDocument with the actual document data
        $this->uploadedDocument = $document->toArray();

        // Emit an event to notify other components about the successful upload
        $this->dispatch('document-uploaded', $document->id);
    }

    public function render()
    {
        return view('livewire.upload-document');
    }
}