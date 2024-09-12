<?php

namespace App\Livewire;

use App\Lib\PdfProcessor;
use Livewire\Component;
use Illuminate\Support\Str;

class MarkdownConverter extends Component
{
    public string $text = '';
    public string $preview = '';

    public function updatedText()
    {
        $this->preview = Str::markdown($this->text);
    }

    public function downloadAsPdf()
    {
        if ($this->preview !== '') {
            $html = Str::markdown($this->preview);
            $fileName = now()->format('Y-m-d-H-i-s') . '.pdf';
            $filePath = PdfProcessor::generatePdf($html, $fileName);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }

    public function render()
    {
        return view('livewire.markdown-converter');
    }
}