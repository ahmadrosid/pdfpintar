<div class="grid grid-cols-2 gap-2">
    <div class="overflow-hidden rounded-md">
        <embed src="{{ asset(Storage::url($this->document->file_id)) }}" width="100%" height="800px" type="application/pdf">
    </div>
    <livewire:chat-interface />
</div>
