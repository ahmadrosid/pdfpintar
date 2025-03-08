<div x-data="{ showDeleteModal: false, documentToDelete: null }">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700/50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center border-b border-neutral-200 dark:border-neutral-700/50 p-4">
                    <div class="w-[300px]">
                        <x-text-input
                            wire:model.live="search"
                            placeholder="{{__('Search documents')}}" />
                    </div>
                    <livewire:document-upload />
                </div>
                <div class="text-neutral-900 dark:text-neutral-300">
                    @if($documents->isEmpty())
                    <p class="text-center text-neutral-500 dark:text-neutral-400 p-6">{{__('No documents found.')}}</p>
                    @else
                    @foreach($documents as $document)
                    <div class="flex flex-1 flex-col sm:flex-row sm:items-center gap-3 px-3 py-2 border-b dark:border-white/10 hover:bg-black/5 group">
                        <div class="flex-grow min-w-0 flex gap-1 relative isolate">
                            <div class="flex items-center gap-2 p-2">
                                <x-icon-file class="flex-shrink-0 w-5 h-5 opacity-50" />
                                <span class="font-medium text-neutral-900 dark:text-neutral-100 truncate">
                                    <a href="{{ route('documents.show', $document->id) }}" class="group-hover:underline">
                                        <span class="absolute inset-0 "></span>
                                        {{ $document->file_name }}
                                    </a>
                                </span>
                                <span class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ $document->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <button
                                class="text-neutral-400 hover:text-rose-500 transition-colors duration-300"
                                @click="$dispatch('open-modal', 'delete-document-modal'); documentToDelete = {{ $document->id }}">
                                <x-icon-trash class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                    @endforeach
                    <p class="text-neutral-400 px-6 pb-4 dark:text-neutral-500 mt-4 text-sm">{{__('Click to chat with the document')}}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-modal name="delete-document-modal">
        <div class="p-4 dark:bg-neutral-800">
            <div class="mb-3">
                <h2 class="text-xl font-bold dark:text-white">{{__('Delete Document')}}</h2>
            </div>
            <div class="mt-2 text-sm dark:text-white">
                <p>{{__('Are you sure you want to delete this document?')}}</p>
                <p>{{__('This action cannot be undone.')}}</p>
            </div>
            <div class="flex justify-end mt-4 gap-4">
                <x-secondary-button @click="$dispatch('close-modal', 'delete-document-modal');">{{__('Cancel')}}</x-secondary-button>
                <x-danger-button wire:click="deleteDocument(documentToDelete)" @click="$dispatch('close-modal', 'delete-document-modal'); documentToDelete = null;">
                    {{__('Delete')}}
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>