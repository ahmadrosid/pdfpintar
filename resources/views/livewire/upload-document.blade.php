<div>
    <div class="p-4 dark:bg-gray-700">
        <x-primary-button wire:click="$dispatch('open-modal', 'upload-document-modal')" >
            <span class="text-sm">Upload PDF</span>
        </x-primary-button>
    </div>
    <x-modal name="upload-document-modal">
        <div class="p-4 dark:bg-gray-700">
            <div class="mb-3">
                <h2 class="text-xl font-bold dark:text-white">Upload PDF Document</h2>
            </div>
            <div class="mt-2">
                <x-filepond::upload wire:model="file" acceptedFileTypes="application/pdf" maxFileSize="120480000" />
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>
            <div class="flex justify-end mt-4 gap-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button wire:click="uploadDocument" wire:loading.attr="disabled">
                    <span wire:loading.remove>Submit</span>
                    <span wire:loading>Uploading...</span>
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>