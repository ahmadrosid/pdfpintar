<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Process;

class SplitDocumentPdf extends Component
{
    use WithFileUploads;

    /**
     * Array of PDF files to be merged
     * @var array<int, array{
     *   file: ?Livewire\TemporaryUploadedFile,
     *   image: string,
     *   filename: string,
     *   filepath: string
     * }>
     */
    public $pdfs = [];
    

    #[Validate('required|mimes:pdf|max:20240')] // 20MB Max
    public $newPdf;

    /**
     * Array of merged PDF files
     * @var array<int, array{
     *   filepath: string,
     *   filename: string,
     *   date: string
     * }>
     */
    public $mergedPdfs = [];

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

    public function removeMergedPdf($index)
    {
        unset($this->mergedPdfs[$index]);
        $this->mergedPdfs = array_values($this->mergedPdfs);
    }

    public function processMergePdfs()
    {
        if (count($this->pdfs) <= 1) {
            $this->addError('merge', __('You need at least two PDFs to merge.'));
            return;
        }

        try {
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
        } catch (\Throwable $e) {
            logger()->error($e);
            $tempFile = tempnam(sys_get_temp_dir(), 'merged_pdf_');
            $files = implode(' ', array_map(function ($pdfFile) {
                return escapeshellarg($pdfFile['filepath']);
            }, $this->pdfs));
            $command = "pdftk $files output $tempFile";
            $output = null;
            $result = Process::run($command, $output);
            if (!$result->successful()) {
                $this->addError('merge', __('Failed to merge PDFs'));
                logger()->error($result->output());
                return;
            }
        }

        $this->mergedPdfs[] = [
            'filepath' => $tempFile,
            'filename' => 'merged_' . now()->format('Y-m-d_H-i-s') . '.pdf',
            'date' => now(),
        ];
    }

    public function updateOrder($item, $position)
    {
        $movedItem = $this->pdfs[$item['index']];
        array_splice($this->pdfs, $item['index'], 1);
        array_splice($this->pdfs, $position, 0, [$movedItem]);
    }

    public function downloadMergedPdfByIndex($index)
    {
        if (count($this->pdfs) <= 1) {
            $this->addError('merge', __('You need at least two PDFs to merge.'));
            return;
        }

        if (count($this->mergedPdfs) == 0) {
            $this->processMergePdfs();
        }

        $mergedPath = $this->mergedPdfs[$index];
        return response()->download($mergedPath['filepath'], $mergedPath['filename'])->deleteFileAfterSend(true);
    }

    public function downloadMergedPdf()
    {
        if (count($this->pdfs) <= 1) {
            $this->addError('merge', __('You need at least two PDFs to merge.'));
            return;
        }

        if (count($this->mergedPdfs) == 0) {
            $this->processMergePdfs();
        }

        if (count($this->mergedPdfs) == 1) {
            $mergedPath = $this->mergedPdfs[0];
            return response()->download($mergedPath['filepath'], $mergedPath['filename'])->deleteFileAfterSend(true);
        }

        $zip = new \ZipArchive();
        $zipFileName = 'merged_pdfs_' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($this->mergedPdfs as $index => $pdfPath) {
                $zip->addFile($pdfPath['filepath'], $pdfPath['filename']);
            }
            $zip->close();

            foreach ($this->mergedPdfs as $tempFile) {
                unlink($tempFile['filepath']);
            }

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } else {
            $this->addError('merge', __('Failed to create zip file.'));
            return;
        }
    }

    private static function generatePdfThumbnail($filePath)
    {
        $thumbnailPath = storage_path('app/public/thumbnails');
        if (!file_exists($thumbnailPath)) {
            Storage::makeDirectory($thumbnailPath);
        }

        $thumbnailImage = now()->format('YmdHis') . '.jpg';
        $outputPath = $thumbnailPath . '/' . $thumbnailImage;
        (new Pdf(storage_path('app/public/' . $filePath)))
            ->selectPage(1)
            ->save($outputPath);

        return asset('storage/thumbnails/'.$thumbnailImage);
    }

    public function render()
    {
        return view('livewire.split-document-pdf');
    }

}
