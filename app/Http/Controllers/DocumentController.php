<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
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
        $newPath = 'documents/' . Auth::user()->id . '/' . Str::random(40) . '.pdf';
        Storage::copy($document->file_path, $newPath);

        // Create a new document record
        $newDocument = Document::create([
            'file_path' => $newPath,
            'file_name' => $document->file_name,
            'file_size' => $document->file_size,
            'user_id' => Auth::user()->id,
            'is_public' => false,
        ]);

        return redirect()->route('documents.show', $newDocument)
            ->with('success', 'Document copied successfully to your library.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:10240'
        ]);

        $uploadedDocuments = [];

        $document = $request->file('document');
        try {
            // Move file to temp directory with client filename
            $filename = $document->getClientOriginalName();
            if (!str_ends_with(strtolower($filename), '.pdf')) {
                $filename .= '.pdf';
            }
            $tempPath = $document->getPathname();
            $newTempPath = sys_get_temp_dir() . '/' . $filename;
            copy($tempPath, $newTempPath);
            logger()->info("Uploading document to OpenAI", [$newTempPath]);
            // Upload to OpenAI first to get file ID
            $response = OpenAI::files()->upload([
                'file' => fopen($newTempPath, 'r'),
                'purpose' => 'assistants',
            ]);

            // Clean up temporary file after upload
            unlink($newTempPath);

            logger()->info("OpenAI file upload response:", [$response]);

            // Store file in S3 with private visibility
            $path = $document->storeAs(
                'documents',
                time() . '_' . $filename,
                ['disk' => 's3', 'visibility' => 'private']
            );

            $uploadedDocument = Document::create([
                'file_id' => $response->id,
                'file_name' => $filename,
                'file_size' => $document->getSize(),
                'file_path' => $path,
                'user_id' => Auth::id(),
            ]);

            $uploadedDocuments[] = $uploadedDocument;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Documents uploaded successfully',
            'documents' => $uploadedDocuments
        ]);
    }
}
