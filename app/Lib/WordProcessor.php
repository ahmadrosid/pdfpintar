<?php

namespace App\Lib;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Str;

class WordProcessor
{
    public static function generateWord($markdown, $fileName)
    {
        $filePath = 'word-temp/' . $fileName;
        $fullPath = storage_path('app/' . $filePath);

        // Ensure the directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $html = Str::markdown($markdown);

        // Create a new Word document
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Add the HTML content to the Word document
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);

        // Save the document
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($fullPath);

        return $fullPath;
    }
}