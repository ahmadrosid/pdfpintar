<div class="container max-w-7xl w-full mx-auto p-6 lg:p-8 my-16">
    <div class="flex gap-4 items-center mb-4">
        <a href="{{route('tools.index')}}" class="hover:opacity-70">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-move-left">
                <path d="M6 8L2 12L6 16" />
                <path d="M2 12H22" />
            </svg>
        </a>
        <a href="{{route('tools.index')}}" class="hover:underline" wire:navigate>
            <h1 class="text-2xl font-bold !leading-tight sm:text-3xl lg:text-4xl text-balance">
                {{__('Split PDF')}}
            </h1>
        </a>
    </div>
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
                    wire:model="newPdf">
                <div class="mt-2">
                    <div class="w-full bg-neutral-200 rounded-full h-2 dark:bg-neutral-700">
                        <div
                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: `0%` }"></div>
                    </div>
                </div>
            </div>
            @error('newPdf') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <div x-data="{ sorting: false, handle: (item, position) => { console.log(item) } }">
                <div class="w-full flex justify-between items-center mb-4">
                    <div>
                        {{__('Total Document:')}} {{count($pdfs)}}
                    </div>
                    <button x-on:click="sorting = !sorting" class="flex items-center gap-2 text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-200">
                        <span
                            x-text="`${sorting ? '{{__('Done sorting')}}' : '{{__('Toggle sorting')}}'}`">
                            {{__('Toggle sorting')}}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
                            <path d="M3 7h18M6 12h12M10 17h4" stroke-width="1.5" stroke-linecap="round"></path>
                        </svg>
                    </button>
                </div>
                <ul x-sort.ghost="$wire.updateOrder($item, $position)"
                    class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
                    x-bind:style="`${sorting ? 'display: flex; flex-direction: column;' : ''}`">
                    @foreach($pdfs as $index => $pdf)
                    <li x-sort:item="@js(['pdf' => $pdf, 'index' => $index])"
                        x-bind:style="`${sorting ? 'max-height: 150px; cursor: grab;' : 'cursor: pointer;'}`">
                        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm overflow-hidden border border-neutral-200 dark:border-neutral-600/75">
                            <div class="p-4"
                                x-bind:class="`${sorting ? 'flex justify-between gap-2 w-full': ''}`">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm font-medium text-neutral-900 dark:text-neutral-200 truncate" title="{{ $pdf['filename'] }}">
                                        {{ Str::limit($pdf['filename'], 20) }}
                                    </span>
                                    <button
                                        wire:click="removePdf({{ $index }})"
                                        class="text-neutral-500 hover:text-red-500"
                                        x-bind:class="`${sorting ? 'hidden': ''}`">
                                        <x-icon-trash />
                                    </button>
                                </div>
                                <div class="bg-neutral-100 dark:bg-neutral-700 rounded-sm p-2 flex items-center justify-center overflow-hidden"
                                    x-bind:class="`${sorting ? 'size-12': 'aspect-[3/4]'}`">
                                    <img src="{{ $pdf['image'] }}" alt="PDF Thumbnail" class="max-w-full max-h-full object-contain">
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="min-h-56 w-full sm:w-[350px] px-4">
            <h3 class="text-xl font-semibold mb-4 text-neutral-900 dark:text-neutral-200">
                {{__('Split Procces')}}
            </h3>
            <div>
                <button
                    wire:click="processMergePdfs"
                    class="w-full bg-neutral-600 text-white py-3 px-4 rounded-lg hover:bg-neutral-700 transition-colors duration-300 mb-4 text-sm flex items-center justify-center gap-3"
                    wire:loading.attr="disabled"
                    wire:target="processMergePdfs">
                    {{__('Merge to One')}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM8.5 12h6"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12.5 15l3-3-3-3"></path>
                    </svg>
                </button>
                <button
                    wire:click="downloadMergedPdf"
                    class="w-full bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 transition-colors duration-300 mt-2 text-sm flex items-center justify-center gap-2"
                    wire:loading.attr="disabled"
                    wire:target="downloadMergedPdf">
                    <x-icon-download />
                    {{__('Download All')}}
                </button>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-2">
                    <span wire:loading wire:target="processMergePdfs">{{__('Please wait merging in progress...')}}</span>
                    @error('merge') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </p>
                <ul class="mt-4">
                    @foreach($mergedPdfs as $pdf)
                    <li class="px-2.5 py-2 bg-neutral-200/50 rounded-md mt-2 cursor-pointer flex justify-between items-center gap-2">
                        <p class="text-sm">{{Str::limit($pdf['filename'], 25)}}</p>
                        <div class="flex">
                            <button class="p-2 hover:opacity-90 hover:bg-neutral-300 rounded-md" wire:click="removeMergedPdf({{ $loop->index }})">
                                <x-icon-trash />
                            </button>
                            <button class="p-2 hover:opacity-90 hover:bg-neutral-300 rounded-md" wire:click="downloadMergedPdfByIndex({{ $loop->index }})">
                                <x-icon-download />
                            </button>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>