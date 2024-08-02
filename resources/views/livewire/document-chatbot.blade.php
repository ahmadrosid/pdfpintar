<div class="grid grid-cols-2 gap-2 h-[94vh] p-2">
    <div class="flex flex-col bg-white h-full overflow-hidden">
        <div id="pdf-viewer" data-url="{{ asset(Storage::url($this->document->file_path)) }}"></div>
    </div>
    <livewire:chat-interface />
</div>
