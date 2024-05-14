<?php

namespace App\Http\Controllers;

use App\Models\Document;

class DocumentController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }
}
