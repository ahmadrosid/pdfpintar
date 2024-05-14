<div>
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
</div>
