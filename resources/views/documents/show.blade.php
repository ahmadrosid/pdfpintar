<x-full-screen-layout>
    <x-slot name="title">
        {{ __('Chat with Document') }}
    </x-slot>
    <div class="h-[7vh] p-2 py-3 items-center flex justify-between">
        <a href="{{ route('documents.index') }}" class="text-gray-900 hover:text-gray-600 font-semibold">
            <span class="left-arrow">&#x2190;</span> {{ $document->file_name }}
        </a>
        <div class="flex items-center gap-4">
            <a href="{{ route('profile') }}" class="text-gray-800 hover:text-gray-500">
                Share
            </a>
            <a href="{{ route('documents.index') }}" class="text-gray-800 hover:text-gray-500 pr-3">
                History
            </a>
        </div>
    </div>
    <livewire:document-chatbot :document="$document" />
</x-full-screen-layout>
