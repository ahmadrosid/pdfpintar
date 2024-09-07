<div x-data="{ showDeleteModal: false, documentToDelete: null }">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700/50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center border-b border-neutral-200 dark:border-neutral-700/50">
                    <div class="px-4 w-[300px]">
                        <x-text-input 
                            wire:model.live="search" 
                            placeholder="{{__('Search documents')}}" />
                    </div>
                    <livewire:upload-document />
                </div>
                <div class="p-6 text-neutral-900 dark:text-neutral-300">
                    @if($documents->isEmpty())
                        <p class="text-center text-neutral-500 dark:text-neutral-400">{{__('No documents found.')}}</p>
                    @else
                        @foreach($documents as $document)
                        <div class="flex items-center gap-2 p-2 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-600">
                            <a class="flex-1" href="{{ route('documents.show', $document->id) }}">
                                <div class="flex gap-2">
                                    <x-icon-file />
                                    {{ $document->file_name }}
                                </div>
                            </a>
                            <div class="flex gap-3 items-center">
                                <a href="{{ route('documents.show', $document->id) }}">
                                    {{ $document->created_at->diffForHumans() }}
                                </a>
                                <button class="hover:text-rose-500 px-2" @click="$dispatch('open-modal', 'delete-document-modal'); documentToDelete = {{ $document->id }}">
                                    <x-icon-trash />
                                </button>
                            </div>
                        </div>
                        @endforeach
                        <p class="text-neutral-400 dark:text-neutral-500 mt-4 text-sm">{{__('Click to chat with the document')}}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-modal name="delete-document-modal">
        <div class="p-4 dark:bg-neutral-800">
            <div class="mb-3">
                <h2 class="text-xl font-bold dark:text-white">Delete Document</h2>
            </div>
            <div class="mt-2 text-sm dark:text-white">
                <p>Are you sure you want to delete this document?</p>
                <p>This action cannot be undone.</p>
            </div>
            <div class="flex justify-end mt-4 gap-4">
                <x-secondary-button @click="$dispatch('close-modal', 'delete-document-modal');">Cancel</x-secondary-button>
                <x-danger-button @click="$wire.deleteDocument(documentToDelete).then(() => { $dispatch('close-modal', 'delete-document-modal'); documentToDelete = null; })">
                    Delete
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>