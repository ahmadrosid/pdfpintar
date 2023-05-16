<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Jobs\ProcessEmbeddingDocument;
use App\Models\Document;
use App\Models\User;
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
        $data = $user->documents()->get()->toArray();
        $documents = collect($data)->map(function ($item) {
            return [
                'id' => $item['id'],
                'path' => str_replace("public", "storage", asset($item['path'])),
                'title' => $item['title'],
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

        return back()->with('path', str_replace("public/documents", "", $path));
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $data = [
            'id' => $document['id'],
            'path' => str_replace("public", "storage", asset($document['path'])),
            'title' => $document['title'],
        ];

        return Inertia::render('Documents/Show', [
            'document' => $data,
            'chat' => session("chat")
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        //
    }
}
