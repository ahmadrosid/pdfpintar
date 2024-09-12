<?php

namespace App\Lib;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class PdfProcessor
{
    public static function generatePdf($htmlMarkdown, $fileName)
    {
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
        $html = $style . $htmlMarkdown;
        
        $storagePath = storage_path('app/public/pdf-temp');
        
        if (!File::isDirectory($storagePath)) {
            File::makeDirectory($storagePath, 0755, true, true);
        }
        
        $filePath = $storagePath . '/' . $fileName;
        
        Pdf::loadHTML($html)->setPaper('a4')->save($filePath);

        return $filePath;
    }
}