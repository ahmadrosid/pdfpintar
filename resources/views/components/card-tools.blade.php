@php
$services = [
    [
        'title' => __('Merge PDF'),
        'description' => __('Merge PDFs in the order you want easily and quickly.'),
        'imageUrl' => '/icons/merge-pdf.svg',
        'href' => route('tools.merge-pdf'),
        'coomin_soon' => false,
        'premium' => false,
    ],
    [
        'title' => __('Split PDF'),
        'description' => __('Separate one page or a whole set for easy conversion into independent PDF files.'),
        'imageUrl' => '/icons/split-pdf.svg',
        'href' => route('tools.split-pdf'),
        'coomin_soon' => true,
        'premium' => false,
    ],
    [
        'title' => __('PDF to JPG'),
        'description' => __('Convert each page of PDF into a JPG image.'),
        'imageUrl' => '/icons/pdf-to-jpg.svg',
        'href' => route('tools.index'),
        'coomin_soon' => true,
        'premium' => false,
    ],
    [
        'title' => __('Image to PDF'),
        'description' => __('Convert JPG or PNG images to PDF in seconds. Easily adjust orientation and margins.'),
        'imageUrl' => '/icons/jpg-to-pdf.svg',
        'href' => route('tools.index'),
        'coomin_soon' => true,
        'premium' => false,
    ],
    [
        'title' => __('Rotate PDF'),
        'description' => __('Rotate your PDFs the way you need them. You can even rotate multiple PDFs at once.'),
        'imageUrl' => '/icons/rotate-pdf.svg',
        'href' => route('tools.index'),
        'coomin_soon' => true,
        'premium' => false,
    ],
    [
        'title' => __('Compress PDF'),
        'description' => __('Reduce file size while optimizing for maximal PDF quality.'),
        'imageUrl' => '/icons/compress-pdf.svg',
        'href' => route('tools.index'),
        'coomin_soon' => true,
        'premium' => true,
    ],
    [
        'title' => __('Word to PDF'),
        'description' => __('Make DOC and DOCX files easy to read by converting them to PDF.'),
        'imageUrl' => '/icons/word-to-pdf.svg',
        'href' => route('tools.index'),
        'coomin_soon' => true,
        'premium' => true,
    ],
    [
        'title' => __('Powerpoint to PDF'),
        'description' => __('Make PPT and PPTX slideshows to view by converting them to PDF.'),
        'imageUrl' => '/icons/powerpoint-to-pdf.svg',
        'href' => route('tools.index'),
        'coomin_soon' => true,
        'premium' => true,
    ],
];
@endphp

<div class="grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 my-12">
    @foreach ($services as $index => $card)
    <a wire:navigate href="{{ $card['href'] }}" 
        class="relative flex flex-col gap-2 rounded-lg border-2 border-transparent bg-neutral-200/75 p-4 text-center outline-none transition-all hover:border-neutral-500 dark:hover:border-neutral-600  dark:bg-neutral-700 dark:shadow-black/25 lg:max-w-xs">
        <div class="mx-auto mt-2">
            <img src="{{ $card['imageUrl'] }}" alt="{{ $card['title'] }}">
        </div>
        <h5 class="text-xl font-semibold mt-1">{{ $card['title'] }}</h5>
        <p class="text-sm text-foreground/60">{{ $card['description'] }}</p>

        @if($card['premium'])
            <span class="absolute right-4 top-4 rounded-lg border border-red px-2 pb-1 pt-0.5 text-xs font-semibold leading-3 text-red">
                Upgrade
            </span>
        @endif
    </a>
    @endforeach
</div>
