<?php

namespace App\Livewire;

use Livewire\Component;

class ModalDemo extends Component
{
    public function render()
    {
        return <<<'blade'
            <div>
                <button class="hover:border-primary hover:text-gray-700 font-medium leading-6 text-gray-600 border-b-2" wire:click="$dispatch('open-modal', 'upload-document-modal')">
                    Watch Demo
                </button>
                <div>
                    <x-modal name="upload-document-modal" :show="false" maxWidth="2xl">
                        <div class="px-4">
                            <div class="py-3 text-left">
                                <h2 class="text-xl font-bold">Watch Demo</h2>
                            </div>
                            <iframe
                                width="640"
                                height="400"
                                class="mx-auto rounded-md my-3 overflow-hidden aspect-video"
                                src="https://www.youtube.com/embed/2AR02P5-RQk?si=QfX2QzGGt3_ZZHW-"
                                title="Demo PDFPINTAR"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowFullScreen
                            ></iframe>
                            <div class="py-3 flex justify-end gap-2">
                                <x-primary-button>Get Started</x-primary-button>
                                <x-secondary-button wire:click="$dispatch('close-modal', 'upload-document-modal')">Close</x-secondary-button>
                            </div>
                        </div>
                    </x-modal>
                </div>
            </div>
blade;
    }
}
