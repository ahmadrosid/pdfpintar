<div class="flex flex-col-reverse sm:flex-row gap-4">
    <div class="flex-1">
        <div
            x-data="{ 
                isHovering: false,
                progress: {},
                updateProgress(filename, progress) {
                    this.progress[filename] = progress;
                }
            }"
            @dragover.prevent="isHovering = true"
            @dragleave.prevent="isHovering = false"
            @drop.prevent="isHovering = false; $wire.upload('newPdf', $event.dataTransfer.files[0])"
            :class="{ 'bg-neutral-200 dark:bg-neutral-600': isHovering }"
            class="flex justify-center items-center h-56 bg-neutral-100 dark:bg-neutral-700 rounded-lg border-2 border-dashed border-neutral-300 dark:border-neutral-600 cursor-pointer hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors duration-300 mb-4">
            <label for="pdf-upload" class="w-full h-full flex items-center justify-center cursor-pointer">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <h2 class="mt-2 text-sm font-medium text-neutral-900 dark:text-neutral-200">{{ __('Click to upload PDF') }}</h2>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ __('or drag and drop') }}</p>
                </div>
            </label>
            <input
                id="pdf-upload"
                type="file"
                class="hidden"
                accept=".pdf"
                wire:model="newPdf"
                x-on:livewire-upload-start="updateProgress($event.detail.filename, 0)"
                x-on:livewire-upload-finish="updateProgress($event.detail.filename, 100)"
                x-on:livewire-upload-error="updateProgress($event.detail.filename, 0)"
                x-on:livewire-upload-progress="updateProgress($event.detail.filename, $event.detail.progress)">
            <div class="mt-2">
                <div class="w-full bg-neutral-200 rounded-full h-2 dark:bg-neutral-700">
                    <div
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${progress[$event.detail.filename] || 0}%` }"></div>
                </div>
            </div>
        </div>
        @error('newPdf') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="w-full flex justify-between items-center mb-4">
            <div>
                {{__('Total Document:')}} {{count($pdfs)}}
            </div>
            <button wire:click="toggleSorting" class="flex items-center gap-2 text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-200">
                {{__('Toggle sorting')}}
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
                    <path d="M3 7h18M6 12h12M10 17h4" stroke-width="1.5" stroke-linecap="round"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($pdfs as $index => $pdf)
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm overflow-hidden border border-neutral-200">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-200 truncate" title="{{ $pdf['filename'] }}">
                            {{ Str::limit($pdf['filename'], 20) }}
                        </span>
                        <button wire:click="removePdf({{ $index }})" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="bg-neutral-100 dark:bg-neutral-700 rounded-sm p-2 flex items-center justify-center aspect-[3/4] overflow-hidden">
                        <img src="{{ $pdf['image'] }}" alt="PDF Thumbnail" class="max-w-full max-h-full object-contain">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="h-56 bg-white w-full sm:w-64 dark:bg-neutral-800 p-4 rounded-lg shadow-sm border border-neutral-200">
        <h3 class="text-lg font-semibold mb-4 text-neutral-900 dark:text-neutral-200">
            {{__('Merge Progress')}}
        </h3>
        <div class="mb-4" x-data="{showProgressBar: false}">
            <div class="w-full bg-neutral-200 rounded-full h-2 dark:bg-neutral-700" x-show="showProgressBar">
                <div
                    class="bg-teal-600 h-2 rounded-full transition-all duration-300"
                    style="width: 0%"
                    wire:loading.class="animate-pulse"
                    wire:target="mergePdfs"></div>
            </div>
            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2" x-data="{showSuccessMessage: false}">
                <span wire:loading wire:target="mergePdfs">{{__('Please wait merging in progress...')}}</span>
                @error('merge') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <span x-show="showSuccessMessage" wire:loading.remove wire:target="mergePdfs" class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-teal-600">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    {{__('Ready to merge')}}
                </span>
            </p>
        </div>
        <div>
            <button
                wire:click="mergePdfs"
                class="w-full bg-neutral-600 text-white py-2 px-4 rounded-lg hover:bg-neutral-700 transition-colors duration-300 mb-2 text-sm"
                wire:loading.attr="disabled"
                wire:target="mergePdfs">
                {{__('Merge PDFs')}}
            </button>
            <button
                wire:click="downloadMergedPdf"
                class="w-full bg-teal-600 text-white py-2 px-4 rounded-lg hover:bg-teal-700 transition-colors duration-300 mt-2 text-sm flex items-center justify-center gap-2"
                wire:loading.attr="disabled"
                wire:target="downloadMergedPdf">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" x2="12" y1="15" y2="3" />
                </svg>
                {{__('Download All')}}
            </button>
        </div>
    </div>
</div>