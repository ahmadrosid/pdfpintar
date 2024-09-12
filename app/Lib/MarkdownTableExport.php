<?php

namespace App\Lib;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use League\CommonMark\CommonMarkConverter;
use DOMException;
use Illuminate\Support\Str;

class MarkdownTableExport implements FromArray, WithHeadings
{
    protected $markdown;
    protected $data = [];
    protected $headings = [];

    public function __construct($markdown)
    {
        $this->markdown = $markdown;
        $this->parseMarkdown();
    }

    protected function parseMarkdown()
    {
        $html = Str::markdown($this->markdown);
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();

        $tables = $dom->getElementsByTagName('table');

        if ($tables->length === 0) {
            throw new \Exception("No table found in the provided Markdown.");
        }

        $table = $tables->item(0);
        $rows = $table->getElementsByTagName('tr');

        foreach ($rows as $rowIndex => $row) {
            $cols = $row->getElementsByTagName('td');
            if ($cols->length == 0) {
                $cols = $row->getElementsByTagName('th');
            }
            
            $rowData = [];
            foreach ($cols as $col) {
                $rowData[] = trim($col->textContent);
            }
            
            if ($rowIndex === 0) {
                $this->headings = $rowData;
            } else {
                $this->data[] = $rowData;
            }
        }
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
