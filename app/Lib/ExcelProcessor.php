<?php

namespace App\Lib;

use Maatwebsite\Excel\Facades\Excel;

class ExcelProcessor
{
    public static function generateExcel($markdown, $fileName)
    {
        $filePath = 'excel-temp/'. $fileName;

        Excel::store(new MarkdownTableExport($markdown), $filePath, 'local'); 

        return storage_path('app/'. $filePath);
    }
}
