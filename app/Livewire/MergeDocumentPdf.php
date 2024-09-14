<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;

class MergeDocumentPdf extends Component
{
    use WithFileUploads;

    // public $pdfs = [];
    public $pdfs = [
        [
            "file" => null,
            "image" => "http://127.0.0.1:8000/storage/thumbnails/20240914092718.jpg",
            "filename" => "2024-09-12-08-07-24.pdf",
            "filepath" => "/Users/ahmadrosid/github.com/ahmadrosid/Products/pdfpintar/storage/app/livewire-tmp/k2GSo3vFhPaqzVZy8Hpttxs8WSUhsb-metaMjAyNC0wOS0xMi0wOC0wNy0yNC5wZGY=-.pdf"
        ],
        [
            "file" => null,
            "image" => "http://127.0.0.1:8000/storage/thumbnails/20240914092721.jpg",
            "filename" => "EasyLaunchAI - INVOICE.pdf",
            "filepath" => "/Users/ahmadrosid/github.com/ahmadrosid/Products/pdfpintar/storage/app/livewire-tmp/FYqZcAfatRVYdCkCe48fy6Gvg5q0B1-metaRWFzeUxhdW5jaEFJIC0gSU5WT0lDRS5wZGY=-.pdf"
        ],
        [
            "file" => null,
            "image" => "http://127.0.0.1:8000/storage/thumbnails/20240914092724.jpg",
            "filename" => "Ahmad Rosid Resume.pdf",
            "filepath" => "/Users/ahmadrosid/github.com/ahmadrosid/Products/pdfpintar/storage/app/livewire-tmp/ih4lfFF5EgCZ9XVsU7uiuVqRHqsQT7-metaQWhtYWQgUm9zaWQgUmVzdW1lLnBkZg==-.pdf"
        ],
        [
            "file" => null,
            "image" => "http://127.0.0.1:8000/storage/thumbnails/20240914092728.jpg",
            "filename" => "Kartu Keluarga.pdf",
            "filepath" => "/Users/ahmadrosid/github.com/ahmadrosid/Products/pdfpintar/storage/app/livewire-tmp/pFQ99hheVgNsOw8Ai8UzkyWdfkVJft-metaS2FydHUgS2VsdWFyZ2EucGRm-.pdf"
        ]
    ];


    #[Validate('required|mimes:pdf|max:20240')] // 20MB Max
    public $newPdf;

    public $mergedPdfs;

    public function updatedNewPdf()
    {
        $this->validate();
        $path = $this->newPdf->store('pdfs', 'public');
        $this->pdfs[] = [
            'file' => $this->newPdf,
            'image' => $this->generatePdfThumbnail(
                $path,
            ),
            'filename' => $this->newPdf->getClientOriginalName(),
            'filepath' => $this->newPdf->getRealPath(),
        ];

        $this->newPdf = null;
        $this->dispatch('pdf-added');
    }
    
    public function removePdf($index)
    {
        dd($this->pdfs);
        unset($this->pdfs[$index]);
        $this->pdfs = array_values($this->pdfs);
    }

    public function mergePdfs()
    {
        if (count($this->pdfs) <= 1) {
            sleep(2);
            $this->addError('merge', __('You need at least two PDFs to merge.'));
            return;
        }

        $pdf = new Fpdi();

        foreach ($this->pdfs as $pdfFile) {
            $pageCount = $pdf->setSourceFile($pdfFile['filepath']);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'merged_pdf_');
        $pdf->Output($tempFile, 'F');
        $this->mergedPdfs[] = $tempFile;
    }

    public function downloadMergedPdf()
    {
        if (count($this->pdfs) <= 1) {
            $this->addError('merge', __('You need at least two PDFs to merge.'));
            return;
        }

        $mergedPath = $this->mergedPdfs[0];
        return response()->download($mergedPath, 'merged.pdf')->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.merge-document-pdf');
    }

    private static function generatePdfThumbnail($filePath)
    {
        $destinationPath = storage_path('app/public/' . $filePath);
        $thumbnailPath = storage_path('app/public/thumbnails');
        $thumbnailImage = now()->format('YmdHis') . '.jpg';

        if (!file_exists($thumbnailPath)) {
            Storage::makeDirectory($thumbnailPath);
        }

        $pdf = new Pdf($destinationPath);
        $imagick = $pdf->selectPage(1)
                       ->getImageData($destinationPath, 1);

        $outputPath = $thumbnailPath . '/' . $thumbnailImage;
        
        $imagick->setImageFormat('jpg');
        $imagick->writeImage($outputPath);

        return asset('storage/thumbnails/'.$thumbnailImage);
    }
}