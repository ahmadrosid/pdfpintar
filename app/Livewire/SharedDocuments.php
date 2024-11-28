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
        $document = Document::findOrFail($documentId);
        $this->authorize('manageSharing', $document);

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

    public function deleteSharedDocument($sharingToken)
    {
        $document = Document::where('sharing_token', $sharingToken)
            ->firstOrFail();
        
        $this->authorize('manageSharing', $document);

        $document->update([
            'sharing_token' => null,
            'is_public' => false,
        ]);

        session()->flash('message', 'Document sharing has been removed.');
    }

    public function render()
    {
        $sharedDocuments = Document::where('user_id', auth()->id())
            ->whereNotNull('sharing_token')
            ->latest()
            ->paginate(10);

        return view('livewire.shared-documents', [
            'documents' => $sharedDocuments
        ])->layout('layouts.app');
    }
}
