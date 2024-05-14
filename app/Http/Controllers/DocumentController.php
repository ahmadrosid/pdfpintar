<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

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
