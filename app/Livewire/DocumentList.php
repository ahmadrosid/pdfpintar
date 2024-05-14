<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;

class DocumentList extends Component
{
    public $documents;

    public function boot()
    {
        $this->documents = Document::where('user_id', auth()->id())->get();
    }

    public function render()
    {
        return view('livewire.document-list');
    }
}
