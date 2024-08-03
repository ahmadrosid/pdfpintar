<div>
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
                                <button class="hover:text-rose-500 px-2" wire:click="deleteDocument({{ $document->id }})" wire:loading.attr="disabled">
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
</div>