<x-full-screen-layout>
    <x-slot name="title">
        {{ $document->file_name }}
    </x-slot>
    <div>
        <div class="h-[6vh] p-2 py-3 items-center flex justify-between pt-4">
            <div class="text-neutral-900 font-semibold dark:text-neutral-200">
                {{ $document->file_name }}
            </div>
            <div class="text-sm text-neutral-500">
                {{ __('Shared Document') }}
            </div>
        </div>
        <div class="flex flex-col lg:grid lg:grid-cols-2 gap-2 h-auto sm:h-[94vh] p-2">
            <div class="order-2 lg:order-1 flex-grow lg:flex-grow-0 flex flex-col bg-white h-full overflow-hidden dark:bg-neutral-700">
                <livewire:public-chat-interface :document="$document" />
            </div>
            <div class="order-1 lg:order-2 flex-grow lg:flex-grow-0 h-[900px] sm:h-auto">
                <div id="pdf-viewer" data-url="{{ $pdfUrl }}" class="w-full h-full"></div>
            </div>
        </div>
    </div>
</x-full-screen-layout>
