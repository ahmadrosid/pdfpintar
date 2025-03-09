<x-full-screen-layout>
    <x-slot name="title">
        {{ __('Chat with Document') }}
    </x-slot>
    <div>
        <div class="h-[6vh] p-2 py-3 items-center flex justify-between pt-4">
            <a href="{{ route('documents.index') }}" class="text-neutral-900 text-sm hover:text-neutral-600 font-semibold dark:text-neutral-200 dark:hover:text-neutral-400">
                <span class="left-arrow">&#x2190;</span> {{ $document->file_name }}
            </a>
            <div class="flex items-center gap-4 text-sm">
                <form action="{{ route('documents.share', $document) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-neutral-800 hover:text-neutral-500 dark:text-neutral-300 dark:hover:text-neutral-400 px-3">
                        @if($document->is_public)
                            {{ __('Make Private') }}
                        @else
                            {{ __('Share') }}
                        @endif
                    </button>
                </form>
                @if($document->is_public)
                    <button onclick="copyShareLink()" class="text-neutral-800 hover:text-neutral-500 dark:text-neutral-300 dark:hover:text-neutral-700 px-3">
                        {{ __('Copy Link') }}
                    </button>
                @endif
            </div>
        </div>
        <div class="flex flex-col lg:grid lg:grid-cols-2 gap-2 h-auto sm:h-[94vh] p-2">
            <div class="order-2 lg:order-1 flex-grow lg:flex-grow-0 flex flex-col bg-white overflow-hidden dark:bg-neutral-700">
                <livewire:chat-page :document="$document" />
            </div>
            <div class="order-1 lg:order-2 flex-grow lg:flex-grow-0 sm:h-auto">
                <div id="pdf-viewer" data-url="{{ $pdfUrl }}" class="w-full h-full"></div>
            </div>
        </div>
    </div>
    @if($document->is_public)
    <script>
        function copyShareLink() {
            const shareUrl = '{{ route('documents.public', $document->sharing_token) }}';
            navigator.clipboard.writeText(shareUrl).then(() => {
                alert('Share link copied to clipboard!');
            });
        }
    </script>
    @endif
</x-full-screen-layout>
