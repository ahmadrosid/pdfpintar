<?php

namespace App\Livewire;

use Livewire\Component;

class ToolIndex extends Component
{
    public $services = [];

    public function mount()
    {
        $this->services = [
            [
                'title' => __('Merge PDF'),
                'description' => __('Merge PDFs in the order you want easily and quickly.'),
                'imageUrl' => '/icons/merge-pdf.svg',
                'href' => route('tools.merge-pdf'),
            ],
            [
                'title' => __('Split PDF'),
                'description' => __('Separate one page or a whole set for easy conversion into independent PDF files.'),
                'imageUrl' => '/icons/split-pdf.svg',
                'href' => route('tools.index'),
            ],
            [
                'title' => __('PDF to JPG'),
                'description' => 'Convert each page of PDF into a JPG image.',
                'imageUrl' => '/icons/pdf-to-jpg.svg',
                'href' => route('tools.index'),
            ],
            [
                'title' => 'JPG to PDF',
                'description' => 'Convert JPG images to PDF in seconds. Easily adjust orientation and margins.',
                'imageUrl' => '/icons/jpg-to-pdf.svg',
                'href' => route('tools.index'),
            ],
            [
                'title' => 'Rotate PDF',
                'description' => 'Rotate your PDFs the way you need them. You can even rotate multiple PDFs at once.',
                'imageUrl' => '/icons/rotate-pdf.svg',
                'href' => route('tools.index'),
            ],
            [
                'title' => 'Compress PDF',
                'description' => 'Reduce file size while optimizing for maximal PDF quality.',
                'imageUrl' => '/icons/compress-pdf.svg',
                'href' => route('tools.index'),
                'premium' => true,
            ],
            [
                'title' => 'Word to PDF',
                'description' => 'Make DOC and DOCX files easy to read by converting them to PDF.',
                'imageUrl' => '/icons/word-to-pdf.svg',
                'href' => route('tools.index'),
                'premium' => true,
            ],
            [
                'title' => 'Powerpoint to PDF',
                'description' => 'Make PPT and PPTX slideshows to view by converting them to PDF.',
                'imageUrl' => '/icons/powerpoint-to-pdf.svg',
                'href' => route('tools.index'),
                'premium' => true,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.tool-index');
    }
}
