<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;

class DocumentsTableSeeder extends Seeder
{
    public function run()
    {
        $documents = [
            [
                'file_id' => '1',
                'file_name' => 'document1.pdf',
                'file_size' => 2048,
                'user_id' => 1,
            ],
            [
                'file_id' => '2',
                'file_name' => 'document2.docx',
                'file_size' => 4096,
                'user_id' => 1,
            ],
            [
                'file_id' => '3',
                'file_name' => 'document3.xlsx',
                'file_size' => 1024,
                'user_id' => 1,
            ],
        ];

        foreach ($documents as $document) {
            Document::create($document);
        }
    }
}