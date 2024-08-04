<div x-data="{ showDeleteModal: false, documentToDelete: null }">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center border-b border-gray-200">
                    <div class="px-4 w-[300px]">
                        <x-text-input wire:model.live="search" placeholder="Search documents" class="block w-full" />
                    </div>
                    <livewire:upload-document />
                </div>
                <div class="p-6 text-gray-900">
                    @if($documents->isEmpty())
                        <p class="text-center text-gray-500">No documents found.</p>
                    @else
                        @foreach($documents as $document)
                        <div class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100">
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
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-modal name="delete-document-modal">
        <div class="p-4">
            <div class="mb-3">
                <h2 class="text-xl font-bold">Delete Document</h2>
            </div>
            <div class="mt-2 text-sm">
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