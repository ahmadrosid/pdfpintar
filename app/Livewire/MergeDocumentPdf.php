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

    public $pdfs = [];

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