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
    public $isUploading = false;

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|max:12048',
        ]);

        $this->isUploading = true;

        if (env('FILESYSTEM_DISK') == 's3') {
            $filePath = $this->file->storePublicly('documents', 's3');
        } else {
            $filePath = $this->file->store('documents', 'public');
        }
        
        $response = OpenAI::files()->upload([
            'file' => fopen($this->file->getPathname(), 'r'),
            'purpose' => 'assistants',
        ]);

        $fileId = $response->id;

        $document = Document::create([
            'file_path' => $filePath,
            'file_id' => $fileId,
            'file_name' => $this->file->getClientOriginalName(),
            'file_size' => $this->file->getSize(),
            'user_id' => Auth::id(),
        ]);

        $this->reset('file');
        $this->isUploading = false;
        
        $this->dispatch('document-uploaded', $document->id);
        $this->dispatch('close-modal', 'upload-document-modal');
    }

    public function render()
    {
        return view('livewire.upload-document');
    }
}