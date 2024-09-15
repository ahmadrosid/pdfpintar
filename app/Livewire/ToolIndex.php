<?php

namespace App\Livewire;

use Livewire\Component;

class ToolIndex extends Component
{
    public $title = "Tools";

    public function render()
    {
        return view('livewire.tool-index');
    }
}
