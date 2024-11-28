<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function copy(Document $document)
    {
        // Check if document is public
        if (!$document->is_public) {
            abort(403);
        }

        // Copy the file to a new location
        $newPath = 'documents/' . auth()->id() . '/' . Str::random(40) . '.pdf';
        Storage::copy($document->file_path, $newPath);

        // Create a new document record
        $newDocument = Document::create([
            'file_path' => $newPath,
            'file_name' => $document->file_name,
            'file_size' => $document->file_size,
            'user_id' => auth()->id(),
            'is_public' => false,
        ]);

        return redirect()->route('documents.show', $newDocument)
            ->with('success', 'Document copied successfully to your library.');
    }
}
