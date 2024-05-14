<div>
    @foreach($documents as $document)
        <a href="{{ route('documents.show', $document->id) }}">
            <div class="flex items-center justify-between gap-2 p-2 rounded-md hover:bg-gray-100">
                <div class="flex gap-2">
                    <x-icon-file />
                    {{ $document->file_name }}
                </div>
                <div>
                    {{ $document->created_at->diffForHumans() }}
                </div>
            </div>
        </a>
    @endforeach
</div>
