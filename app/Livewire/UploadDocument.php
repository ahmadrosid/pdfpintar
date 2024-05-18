<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class UploadDocument extends Component
{
    use WithFileUploads;

    public $file;
    public $show_modal = false;

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|max:2048',
        ]);

        $file = $this->file;
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        $filePath = $file->store('documents', 'public');
        $response = OpenAI::files()->upload([
            'file' => fopen(storage_path("app/public/" . $filePath), 'r'),
            'purpose' => 'assistants',
        ]);

        $fileId = $response->id;
        $document = Document::create([
            'file_path' => $filePath,
            'file_id' => $fileId,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'user_id' => Auth::id(),
        ]);

        $this->reset('file'); // Reset the file input
        $this->show_modal = false;
        $this->dispatch('close-modal', 'upload-document-modal');
    }

    public function render()
    {
        return view('livewire.upload-document');
    }
}
