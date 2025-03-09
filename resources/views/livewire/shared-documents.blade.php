<div>
    <x-slot name="header">
        <h2 class="font-semibold text-lg leading-tight">
            {{ __('Shared chats') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700/50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center border-b border-neutral-200 dark:border-neutral-700/50">
                    <div class="px-4 py-4">
                        <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">{{ __('Shared Documents') }}</h2>
                    </div>
                </div>
                <div class="text-neutral-900 dark:text-neutral-300">
                    @if($documents->isEmpty())
                        <p class="text-center text-neutral-500 dark:text-neutral-400 p-6">{{ __('No shared documents yet.') }}</p>
                    @else
                        @foreach($documents as $document)
                            <div class="flex flex-1 flex-col sm:flex-row sm:items-center gap-3 px-6 py-2 border-b dark:border-white/10 hover:bg-black/5 group">
                                <div class="flex-grow min-w-0 relative isolate">
                                    <div class="flex items-center gap-3 text-sm">
                                        <x-icon-file class="flex-shrink-0 size-5" />
                                        <a href="{{ route('documents.show', $document->id) }}" class="block group-hover:underline">
                                            <span class="absolute inset-0 z-10"></span>
                                            <span class="font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ $document->file_name }}</span>
                                        </a>
                                        <span class="px-1.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $document->is_public ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $document->is_public ? __('Public') : __('Private') }}
                                        </span>
                                        <span class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ $document->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button 
                                        wire:click="toggleShare({{ $document->id }})"
                                        class="text-neutral-400 hover:text-indigo-500 transition-colors duration-300 p-1"
                                        title="{{ $document->is_public ? __('Make Private') : __('Make Public') }}"
                                    >
                                        @if($document->is_public)
                                            <x-icon-unlock  class="size-5"/>
                                        @else
                                            <x-icon-lock class="size-5"/>
                                        @endif
                                    </button>
                                    @if($document->is_public)
                                        <button 
                                            x-data="{ copied: false }"
                                            @click="
                                                navigator.clipboard.writeText('{{ route('documents.public', $document->sharing_token) }}');
                                                copied = true;
                                                setTimeout(() => copied = false, 2000)
                                            "
                                            class="text-neutral-400 hover:text-blue-500 transition-colors duration-300 p-1"
                                            title="{{ __('Copy Share Link') }}"
                                        >
                                            <span x-show="copied" x-cloak>
                                                <x-icon-check class="size-5"/>
                                            </span>
                                            <span x-show="!copied">
                                                <x-icon-copy class="size-5" />
                                            </span>
                                        </button>
                                    @endif
                                    <button 
                                        wire:click="deleteSharedDocument('{{ $document->sharing_token }}')"
                                        class="text-neutral-400 hover:text-red-500 transition-colors duration-300 p-1"
                                        title="{{ __('Delete Shared Document') }}"
                                    >
                                        <x-icon-trash class="size-5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        <div class="px-6 py-3">
                            {{ $documents->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
