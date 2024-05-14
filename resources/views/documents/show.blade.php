<x-full-screen-layout>
    <x-slot name="title">
        {{ __('Chat with Document') }}
    </x-slot>
    <div class="h-[5vh] p-2 flex justify-between">
        <a href="{{ route('documents.index') }}" class="text-gray-900 hover:text-gray-600 font-semibold">
            PDFPINTAR
        </a>
        <div class="flex items-center gap-4">
            <a href="{{ route('profile') }}" class="text-gray-800 hover:text-gray-500">
                Share
            </a>
            <a href="{{ route('documents.index') }}" class="text-gray-800 hover:text-gray-500 underline">
                Go back <span class="px-1">&#x2192;</span>
            </a>
        </div>
    </div>
    <livewire:document-chatbot :document="$document" />
</x-full-screen-layout>
