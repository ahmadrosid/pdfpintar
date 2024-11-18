<?php

namespace App\Livewire;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class SharedDocuments extends Component
{
    use WithPagination;

    public function toggleShare($documentId)
    {
        $document = Document::where('user_id', auth()->id())
            ->findOrFail($documentId);

        if (!$document->sharing_token) {
            $document->update([
                'sharing_token' => Str::random(32),
                'is_public' => true,
            ]);
        } else {
            $document->update([
                'is_public' => !$document->is_public
            ]);
        }
    }

    public function copyShareLink($documentId)
    {
        $document = Document::where('user_id', auth()->id())
            ->where('is_public', true)
            ->findOrFail($documentId);

        return route('documents.public', $document->sharing_token);
    }

    public function render()
    {
        $sharedDocuments = Document::where('user_id', auth()->id())
            ->whereNotNull('sharing_token')
            ->latest()
            ->paginate(10);

        return view('livewire.shared-documents', [
            'documents' => $sharedDocuments
        ]);
    }
}
