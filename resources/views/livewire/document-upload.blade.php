<div>
    <div class="p-4">
        <x-primary-button class="dark:border-neutral-700/75 py-2" wire:click="$dispatch('open-modal', 'upload-document-modal')" >
            <span class="text-sm">{{__('Upload PDF')}}</span>
        </x-primary-button>
    </div>
    <x-modal name="upload-document-modal">
        <div class="p-4 dark:bg-neutral-700">
            <div class="mb-3">
                <h2 class="text-xl font-bold dark:text-white">{{__('Upload your PDF file')}}</h2>
            </div>
            <div class="mt-2">
                @if (session()->has('error'))
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-800/20 dark:text-red-400" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                <input type="file" 
                    wire:model.live="document" 
                    accept="application/pdf" 
                    class="
                        file:py-2 file:px-4 file:bg-neutral-100 file:rounded-md file:cursor-pointer p-px file:border-0
                        block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-gray-400
                    "
                />
                <x-input-error :messages="$errors->get('document')" class="mt-2" />

                <div wire:loading wire:target="document" class="relative pt-1 mt-4 w-full">
                    Uploading...
                </div>
            </div>
            <div class="flex justify-end mt-4 gap-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">{{__('Cancel')}}</x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>