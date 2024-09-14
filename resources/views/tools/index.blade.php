
@php
$services = [
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
        'title' => 'PDF to JPG',
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
@endphp

<x-full-screen-layout>
    <div class="relative sm:flex sm:justify-center min-h-screen bg-square bg-center bg-white dark:bg-neutral-800 selection:bg-neutral-500 selection:text-white">
        <div class="top-0 inset-x-0 absolute bg-gradient-to-b from-white to-white/0 dark:from-neutral-800 dark:to-neutral-800">
            <x-public-navigation />
        </div>

        <div class="max-w-6xl w-full mx-auto p-6 lg:p-8 my-16">
            <div class="mx-auto max-w-6xl space-y-4 text-center mb-12">
                <h1 class="text-4xl font-bold !leading-tight sm:text-5xl lg:text-6xl text-balance">
                    {{__('Your all-in-one PDF toolkit')}}
                </h1>
                <p class="text-lg text-neutral-800 dark:text-white lg:text-xl text-balance">
                    {{__('Transform your PDF experience with our cost-free toolkit. From merging to compression, access a full spectrum of easy-to-use features.')}}
                </p>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 my-12">
                @foreach ($services as $index => $card)
                    @include('components.tools.card', [
                        'title' => $card['title'],
                        'description' => $card['description'],
                        'imageUrl' => $card['imageUrl'],
                        'href' => $card['href'],
                        'premium' => $card['premium'] ?? false,
                    ])
                @endforeach
            </div>
        </div>
    </div>

    <x-public-footer />
</x-full-screen-layout>