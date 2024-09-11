<x-full-screen-layout>
    <x-slot name="title">
        {{ __('Chat with Document') }}
    </x-slot>
    <div>
        <div class="h-[6vh] p-2 py-3 items-center flex justify-between pt-4">
            <a href="{{ route('documents.index') }}" class="text-neutral-900 hover:text-neutral-600 font-semibold dark:text-neutral-200 dark:hover:text-neutral-400">
                <span class="left-arrow">&#x2190;</span> {{ $document->file_name }}
            </a>
            <div class="flex items-center gap-4">
                <a href="#" class="text-neutral-800 hover:text-neutral-500 dark:text-neutral-300 dark:hover:text-neutral-700">
                    {{__('History')}}
                </a>
                <a href="{{ route('documents.index') }}" class="text-neutral-800 hover:text-neutral-500 dark:text-neutral-300 dark:hover:text-neutral-700 pr-3">
                    {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="flex flex-col lg:grid lg:grid-cols-2 gap-2 h-auto sm:h-[94vh] p-2">
            <div class="order-2 lg:order-1 flex-grow lg:flex-grow-0 flex flex-col bg-white h-full overflow-hidden dark:bg-neutral-700">
                <div id="pdf-viewer" data-url="{{ $pdfUrl }}" class="w-full h-full"></div>
            </div>
            <div class="order-1 lg:order-2 flex-grow lg:flex-grow-0 h-[900px] sm:h-auto">
                <livewire:chat-interface :document="$document" />
            </div>
        </div>
    </div>
</x-full-screen-layout>