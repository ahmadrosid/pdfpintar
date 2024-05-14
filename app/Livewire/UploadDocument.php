<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class UploadDocument extends Component
{
    use WithFileUploads;

    public $file;

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|max:2048',
        ]);

        $file = $this->file;
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        $document = Document::create([
            'file_id' => $file->store('documents', 'public'),
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'user_id' => Auth::id(),
        ]);

        $this->reset('file'); // Reset the file input
        $this->dispatch('close-modal', detail: 'upload-document-modal');
    }

    public function render()
    {
        return view('livewire.upload-document');
    }
}
