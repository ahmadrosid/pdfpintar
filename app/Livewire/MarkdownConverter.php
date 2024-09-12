<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

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
        if ($this->text !== '') {
            $style = <<<'HTML'
            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
            </style>
            HTML;
            $html = $style . $this->preview;
            $fileName = now()->format('Y-m-d-H-i-s') . '.pdf';
            
            $storagePath = storage_path('app/public/pdf-temp');
            
            if (!File::isDirectory($storagePath)) {
                File::makeDirectory($storagePath, 0755, true, true);
            }
            
            $filePath = $storagePath . '/' . $fileName;
            
            Pdf::loadHTML($html)->setPaper('a4')->save($filePath);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }

    public function render()
    {
        return view('livewire.markdown-converter');
    }
}