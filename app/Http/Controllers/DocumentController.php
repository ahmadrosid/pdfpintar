<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Jobs\ProcessEmbeddingDocument;
use App\Models\Chat;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Inertia\Inertia;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $data = $user->documents()->get();
        $documents = collect($data)->map(function ($item) {
            return [
                'id' => $item->id,
                'path' => str_replace("public", "storage", asset($item->path)),
                'title' => $item->title,
                'created_at' => $item->created_at->diffForHumans()
            ];
        })->all();

        return Inertia::render('Documents/Index', [
            'documents' => $documents
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Documents/Create', [
            'path' => session('path'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('public/documents');
        $user = $request->user();
        $pdf = $user->documents()->create([
            'path' => $path,
            'title' => $file->getClientOriginalName()
        ]);

        $jobId = Queue::push(
            new ProcessEmbeddingDocument($pdf)
        );

        $pdf->job_id = $jobId;
        $pdf->save();

        return back()->with('path', route("documents.show", $pdf->id));
    }

    public function show(Request $request, Document $document)
    {
        $chat = Chat::create([
            'user_id' => $request->user()->id,
            'document_id' => $document->id,
            'title' => $document->title,
        ]);

        return redirect(route('chat.show', $chat->id));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        Chat::query()->where('document_id', $document->id)->delete();
        $document->delete();
        return back()->with("status", "Document {$document->title} deleted!");
    }
}
