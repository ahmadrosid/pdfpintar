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
        <div class="grid grid-cols-2 gap-2 h-[94vh] p-2">
            <div class="flex flex-col bg-white h-full overflow-hidden dark:bg-neutral-700">
                <div id="pdf-viewer" data-url="{{ Storage::url($document->file_path) }}"></div>
            </div>
            <livewire:chat-interface :document="$document" />
        </div>
    </div>
</x-full-screen-layout>