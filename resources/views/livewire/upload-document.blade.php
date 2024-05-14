<div>
    <div class="p-4">
        <button wire:click="$dispatch('open-modal', 'upload-document-modal')" class="bg-gray-800 flex justify-between items-center text-white font-light py-2 px-3 rounded-md">
            <x-icon-square-plus /> <span class="ml-2">Upload Document</span>
        </button>
    </div>
    <x-modal name="upload-document-modal" :show="$show_modal" maxWidth="2xl">
        <div class="p-4">
            <div class="mb-3">
                <h2 class="text-xl font-bold">Upload Document</h2>
            </div>
            <form wire:submit.prevent="uploadDocument">
                <div class="mb-3">
                    <label class="block">
                        <span class="sr-only">Upload Document</span>
                        <input type="file" class="block w-full text-sm text-gray-500
                        file:me-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-600 file:text-white
                        hover:file:bg-blue-700
                        file:disabled:opacity-50 file:disabled:pointer-events-none
                        dark:text-neutral-500
                        dark:file:bg-blue-500
                        dark:hover:file:bg-blue-400
                      "
                               wire:model="file" id="file"
                        >
                    </label>
                </div>
                <x-primary-button type="submit">Submit</x-primary-button>
            </form>
        </div>
    </x-modal>
</div>
